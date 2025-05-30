<?php

namespace App\Http\Controllers;

use App\LoanPayments;
use Illuminate\Http\Request;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class LoanPaymentsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    public function index(Request $request, $loan_id)
    {       

        if (request()->ajax()) {                         

            $loanPayments = LoanPayments::where('loan_id', $loan_id)->select(['id', 'date_pay', 'amount_pay', 'method_pay','observation']);

            return Datatables::of($loanPayments)
            ->addColumn('action',function ($row) {
                return '
                    <button data-href="' . action('App\Http\Controllers\LoanPaymentsController@edit', [$row->id]) . '" class="btn btn-xs btn-primary loan_payments_button"><i class="glyphicon glyphicon-edit"></i> Pagar</button>
                ';
            })
            ->editColumn('amount_pay', function ($row){
                $monto = number_format($row->amount_pay, 2);
                return 'S/. ' . $monto;
            })
            ->rawColumns(['action'])            
            ->make(true);           
        }

        return view('loan.partials.detail', compact('loan_id'));
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $loanPayments = LoanPayments::findOrFail($id);
           
            return view('loan.partials.loan_payments')
                ->with(compact('loanPayments'));
        }
    }

    public function update(Request $request, $id)
    {
        
        if (request()->ajax()) {
            try {
                $input = $request->only(['date_pay', 'amount_pay', 'method_pay', 'observation']);
                

                $loanPayments = LoanPayments::findOrFail($id);
                $loanPayments->date_pay = $input['date_pay'];
                $loanPayments->amount_pay = $input['amount_pay'];
                $loanPayments->method_pay = $input['method_pay'];
                $loanPayments->observation = $input['observation'];
                
                $loanPayments->save();

                $output = ['success' => true,
                    'msg' => 'El cuota se pagó con éxito',
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

    public function create()
    {
        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        $users = User::all();

        return view('loan.create')
                ->with(compact('quick_add', 'is_repair_installed','users'));
    }
}
