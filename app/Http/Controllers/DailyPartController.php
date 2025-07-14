<?php

namespace App\Http\Controllers;

use App\Account;
use App\BusinessLocation;
use App\Contact;
use App\DailyPart;
use App\LoanInternals;
use App\LoanPayments;
use App\Part;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Utils\TransactionUtil;
use App\Utils\CashRegisterUtil;
use DB;

class DailyPartController extends Controller
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

    public function index($id)
    {
        $part = Part::find($id);
        $proveedor = Contact::find($part->proveedor_id);
        $product = Product::find($part->product_id);
        $part_id = $id;

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $daily_parts = DailyPart::where('part_id', $id)
                        ->select(['id','created_at', 'part_id', 'conductor', 'dni','h_inicio','h_final','zona_trabajo','combustible']);

            return Datatables::of($daily_parts)
                ->addColumn(
                    'action',
                    '@can("loan.view")
                    <a href="{{action(\'App\Http\Controllers\DiaryPartController@show\', [$id])}}" class="btn btn-info btn-xs"><i class="fa fa-book"></i> Detalle</a>
                        &nbsp;
                    @endcan
                    @can("loan.update")
                    <button data-href="{{action(\'App\Http\Controllers\DiaryPartController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_part_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("loan.delete")
                        <button data-href="{{action(\'App\Http\Controllers\DiaryPartController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_part_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('parts.partials.detail')->with(compact('proveedor','product','part_id'));
    }

    public function show($id)
    {
        $part = Part::find($id);
        $proveedor = Contact::find($part->proveedor_id);
        $product = Product::find($part->product_id);
        $part_id = $id;

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $daily_parts = DailyPart::where('part_id', $id)
                        ->select(['id','created_at', 'part_id', 'conductor', 'dni','h_inicio','h_final','zona_trabajo','combustible']);

            return Datatables::of($daily_parts)
                ->addColumn(
                    'action',
                    '@can("loan.view")
                    <a href="{{action(\'App\Http\Controllers\DiaryPartController@show\', [$id])}}" class="btn btn-info btn-xs"><i class="fa fa-book"></i> Detalle</a>
                        &nbsp;
                    @endcan
                    @can("loan.update")
                    <button data-href="{{action(\'App\Http\Controllers\DiaryPartController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_part_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("loan.delete")
                        <button data-href="{{action(\'App\Http\Controllers\DiaryPartController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_part_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('parts.partials.detail')->with(compact('proveedor','product','part_id'));
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

        $proveedores = Contact::where('type','supplier')->get();
        $products = Product::where('not_for_selling',1)->get();

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

        return view('parts.create')
                ->with(compact('quick_add', 'is_repair_installed','proveedores','products', 'accounts', 'business_locations', 'bl_attributes','payment_line','payment_types'));
    }

    public function createDailyPart()
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

        $proveedores = Contact::where('type','supplier')->get();
        $products = Product::where('not_for_selling',1)->get();

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

        return view('parts.partials.daily_part')
                ->with(compact('quick_add', 'is_repair_installed','proveedores','products', 'accounts', 'business_locations', 'bl_attributes','payment_line','payment_types'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['proveedor_id', 'product_id', 'observations']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;    

            $parts = Part::create($input);
          
            $output = ['success' => true,
                'data' => $parts,
                'msg' => 'Parte creado con éxito',
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return response()->json($output);
    }

    public function storeDailyPart(Request $request)
    {
        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['proveedor_id', 'product_id', 'observations']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;    

            $parts = Part::create($input);
          
            $output = ['success' => true,
                'data' => $parts,
                'msg' => 'Parte creado con éxito',
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

                $part = Part::where('business_id', $business_id)->findOrFail($id);
                $part->delete();

                $output = ['success' => true,
                    'msg' => 'Parte eliminado con éxito',
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
}
