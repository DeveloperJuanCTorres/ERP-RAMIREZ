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

class DiaryPartController extends Controller
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

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            
            $parts = Part::where('business_id', $business_id)
                        ->select(['id','created_at', 'proveedor_id', 'product_id', 'observations']);
            
            if (!empty(request()->location_id)) {
                $parts->where('business_id', request()->location_id);
            }

            if (!empty(request()->supplier_id)) {
                $parts->where('proveedor_id', request()->supplier_id);
            }

            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                try {
                    $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $parts->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    // Evita romper la app si el formato falla
                    \Log::error('Error al parsear date_range: ' . $e->getMessage());
                }
            }            

            return Datatables::of($parts)
                ->addColumn('action', function ($row) {
                    $buttons = '';

                    if (auth()->user()->can('loan.view')) {
                        $buttons .= '<a href="' . action('App\Http\Controllers\DiaryPartController@show', [$row->id]) . '" class="btn btn-info btn-xs"><i class="fa fa-book"></i> Detalle</a> ';
                    }

                    if (auth()->user()->can('loan.update')) {
                        $buttons .= '<button data-href="' . action('App\Http\Controllers\DiaryPartController@edit', [$row->id]) . '" class="btn btn-xs btn-primary edit_part_button"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</button> ';
                    }

                    if (auth()->user()->can('loan.delete')) {
                        $buttons .= '<button data-href="' . action('App\Http\Controllers\DiaryPartController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_part_button"><i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '</button>';
                    }

                    return $buttons;
                })
                ->editColumn('proveedor_id', function($row) {
                    $proveedor = Contact::find($row->proveedor_id);
                    return $proveedor ? $proveedor->supplier_business_name . $proveedor->name : '';
                })
                ->editColumn('product_id', function($row) {
                    $proveedor = Product::find($row->proveedor_id);
                    return $proveedor ? $proveedor->supplier_business_name . $proveedor->name : '';
                })
                ->editColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })                
                // ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('parts.index')
            ->with(compact('business_locations','suppliers'));;
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

            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                try {
                    $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $daily_parts->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    // Evita romper la app si el formato falla
                    \Log::error('Error al parsear date_range: ' . $e->getMessage());
                }
            }              

            return Datatables::of($daily_parts)
                ->addColumn(
                    'action',
                    '@can("loan.update")
                    <button data-href="{{action(\'App\Http\Controllers\DiaryPartController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_part_button"><i class="glyphicon glyphicon-edit"></i> Pagar</button>
                        &nbsp;
                    @endcan'
                )
                ->addColumn('total', function($row) {
                    return $row->h_final - $row->h_inicio . ' Horas';
                })
                ->editColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                }) 
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

    

    public function storeDailyPart(Request $request, $id)
    {
        if (! auth()->user()->can('loan.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {    
            $input = $request->only(['dni','conductor', 'h_inicio', 'h_final','zona_trabajo','combustible']);           
            $business_id = $request->session()->get('user.business_id');
            $parts = DailyPart::create([
                'business_id' => $business_id,
                'part_id' => $id,
                'conductor' => $input['conductor'],
                'dni' => $input['dni'],
                'h_inicio' => $input['h_inicio'],
                'h_final' => $input['h_final'],
                'zona_trabajo' => $input['zona_trabajo'],
                'combustible' => $input['combustible'],
            ]);
          
            $output = ['success' => true,
                'data' => $parts,
                'msg' => 'Parte Diario creado con éxito :)',
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
