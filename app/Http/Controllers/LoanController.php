<?php

namespace App\Http\Controllers;

use App\Account;
use App\BusinessLocation;
use App\LoanInternals;
use App\LoanPayments;
use App\User;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Utils\TransactionUtil;
use App\Utils\CashRegisterUtil;
use DB;

class LoanController extends Controller
{
    /**
     * Constructor
     *
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, CashRegisterUtil $cashRegisterUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', ];
        $this->cashRegisterUtil = $cashRegisterUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (! auth()->user()->can('loan.view') && ! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $loans = LoanInternals::where('business_id', $business_id)
                        ->select(['created_at', 'user_id', 'amount', 'type', 'time','id', 'tax','total_pay']);

            return Datatables::of($loans)
                ->addColumn(
                    'total_paid',
                    0
                )
                ->addColumn(
                    'action',
                    '@can("loan.view")
                    <a href="{{action(\'App\Http\Controllers\LoanController@show\', [$id])}}" class="btn btn-info btn-xs"><i class="fa fa-book"></i> Detalle</a>
                        &nbsp;
                    @endcan
                    @can("loan.update")
                    <button data-href="{{action(\'App\Http\Controllers\LoanController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_loan_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("loan.delete")
                        <button data-href="{{action(\'App\Http\Controllers\LoanController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_loan_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('user_id', function($row) {
                    $user = User::find($row->user_id);
                    return $user ? $user->first_name . ' ' . $user->last_name : '';
                })
                ->editColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->editColumn('tax', function($row) {
                    return $row->tax . '%';
                })
                ->editColumn('amount', function($row) {
                    return 'S/. ' . number_format($row->amount, 2);
                })
                ->editColumn('total_pay', function($row) {
                    return 'S/. ' . number_format($row->total_pay, 2);
                })
                ->removeColumn('id')
                ->rawColumns([8])
                ->make(false);
        }

        return view('loan.internal');
    }

    public function show($id)
    {
        $loan = LoanInternals::find($id);
        $user = User::find($loan->user_id);
        $loan_id = $id;
        
        return view('loan.partials.detail')->with(compact('user','loan','loan_id'));
    }
    
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        $users = User::all();

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
        
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        return view('loan.create')
                ->with(compact('quick_add', 'is_repair_installed','users', 'accounts', 'business_locations', 'bl_attributes','payment_line','payment_types'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['user_id', 'amount', 'type', 'time', 'tax']);
            $business_id = $request->session()->get('user.business_id');
            $input['total_pay'] = $input['amount'] + $input['amount'] * $input['tax']/100;
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            $tiempo = $input['time'];

            $user_id = $request->session()->get('user.id');

            DB::beginTransaction();

            $expense = $this->transactionUtil->createPrestamoInterno($request, $business_id, $user_id);
           

            $this->transactionUtil->activityLog($expense, 'added');

            DB::commit();

            $loan = LoanInternals::create($input);

            if ($input['type'] == 'dia') {
                $fechaInicio = Carbon::now();
                $amountPay = $input['total_pay']/$input['time'];
                for ($i=0; $i < $tiempo; $i++) { 
                    $fechaInicio = $fechaInicio->copy()->addDays(1);
                    $payments = new LoanPayments();

                    $payments->loan_id = $loan->id;
                    $payments->date_pay = $fechaInicio;
                    $payments->amount_pay = $amountPay;
                    $payments->save();
                }
            }

            if ($input['type'] == 'semana') {
                $fechaInicio = Carbon::now();
                $amountPay = $input['total_pay']/$input['time'];
                for ($i=0; $i < $tiempo; $i++) { 
                    $fechaInicio = $fechaInicio->copy()->addWeeks(1);
                    $payments = new LoanPayments();

                    $payments->loan_id = $loan->id;
                    $payments->date_pay = $fechaInicio;
                    $payments->amount_pay = $amountPay;
                    $payments->save();
                }
            }

            if ($input['type'] == 'mes') {
                $fechaInicio = Carbon::now();
                $amountPay = $input['total_pay']/$input['time'];
                for ($i=0; $i < $tiempo; $i++) { 
                    $fechaInicio = $fechaInicio->copy()->addMonths(1);
                    $payments = new LoanPayments();

                    $payments->loan_id = $loan->id;
                    $payments->date_pay = $fechaInicio;
                    $payments->amount_pay = $amountPay;
                    $payments->save();
                }
            }
            $output = ['success' => true,
                'data' => $loan,
                'msg' => 'Préstamo creado con éxito',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return response()->json($output);
    }

    public function edit($id)
    {
        if (! auth()->user()->can('loan.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $loan = LoanInternals::where('business_id', $business_id)->find($id);

            $users = User::all();
            return view('loan.edit')
                ->with(compact('loan', 'users'));
        }
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('loan.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['user_id', 'amount', 'type', 'time']);
                $business_id = $request->session()->get('user.business_id');

                $loan = LoanInternals::where('business_id', $business_id)->findOrFail($id);
                $loan->user_id = $input['user_id'];
                $loan->amount = $input['amount'];
                $loan->type = $input['type'];
                $loan->time = $input['time'];
                
                $loan->save();

                $output = ['success' => true,
                    'msg' => 'El préstamos se editó con éxito',
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function destroy($id)
    {
        if (! auth()->user()->can('loan.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $loan = LoanInternals::where('business_id', $business_id)->findOrFail($id);
                $loan->delete();

                $output = ['success' => true,
                    'msg' => 'Préstamo eliminado con éxito',
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    // public function prestamoInterno()
    // {
    //     if (request()->ajax()) {
    //         $business_id = request()->session()->get('user.business_id');
    //         $user_id = request()->session()->get('user.id');
    //         $user_name = User::where('id',$user_id)->first();

    //         $loanInternals = LoanInternals::where('business_id', $business_id)->get();

    //         return DataTables::of($loanInternals)     
    //             ->editcolum(
    //                 'user_id',
    //                 '<span>{{$user_name->first_name . " " . $user_name->last_name}}</span>'
    //             )           
    //             ->addColumn(
    //                 'action',
    //                 '@can("user.update")
    //                     <a href="{{action(\'App\Http\Controllers\ManageLoanController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
    //                     &nbsp;
    //                 @endcan
    //                 @can("user.view")
    //                 <a href="{{action(\'App\Http\Controllers\ManageLoanController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
    //                 &nbsp;
    //                 @endcan
    //                 @can("user.delete")
    //                     <button data-href="{{action(\'App\Http\Controllers\ManageLoanController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
    //                 @endcan'
    //             )
    //             ->filterColumn('full_name', function ($query, $keyword) {
    //                 $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
    //             })
    //             ->removeColumn('id')
    //             ->rawColumns(['action', 'username'])
    //             ->make(true);
    //     }
    //     return view('loan.internal');
    // }
}
