<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\WithdrawRequestDataTable;
use App\Exports\WithdrawRequestExport;
use App\Exports\WithdrawRequestapprovedExport;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\Wallet;
use App\Models\WithdrawDetail;
use App\Models\WithdrawRequest;
use App\Notifications\OrderNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class WithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WithdrawRequestDataTable $dataTable)
    {
        if (!auth()->user()->can('withdrawrequest-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $auth = Auth::user();
        $pageTitle = __('message.list_form_title',['form' => __('message.withdrawrequest')] );
        $auth_user = authSession();
        $params = [
            'status' => request('status') ?? null,
        ];
        $assets = ['datatable'];

        $reset_file_button = '<a href="' . route('withdrawrequest.index') . '" class="mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';

        $withdraw_type = request()->input('withdraw_type', 'all');
        $export = '';
        $exportapproved = '';
        if($withdraw_type === 'pending'){
            $export = '<a href="' . route('withdraw-requestexcelmodel') . '" class="mr-1 mt-0 btn btn-sm btn-success text-dark mt-3 pt-2 pb-2 loadRemoteModel"><i class="fas fa-file-csv"></i> ' . __('message.export') . '</a>';
        }
        if($withdraw_type === 'approved'){
            $exportapproved = '<a href="' . route('withdraw-approvedexcelmodel') . '" class="mr-1 mt-0 btn btn-sm btn-success text-dark mt-3 pt-2 pb-2 loadRemoteModel"><i class="fas fa-file-csv"></i> ' . __('message.export') . '</a>';
        }
        $button = '';
        if (($auth->user_type == 'client') && $auth_user->can('withdrawrequest-add')) {
            $button = '<a href="' . route('withdrawrequest.create') . '" class="float-right btn btn-sm btn-primary loadRemoteModel"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.withdrawrequest')]) . '</a>';
        }

        return $dataTable->render('global.withdrawrequest-filter', compact('pageTitle', 'auth_user', 'params', 'reset_file_button', 'assets', 'button','export','exportapproved'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('withdrawrequest-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $auth_user = authSession();
        if($auth_user->user_type == 'client'){

            $pageTitle = __('message.withdraw_money');
            return view('clientside.withdrawpop', compact('pageTitle'));
        }
            return redirect('withdrawrequest');

    }

    public function clientwithdraw()
    {
        if(Auth::user()->user_type == 'client')
        {
         $pageTitle = __('message.withdraw');

            $client = Auth::user()->id;
            $withdrawrequest = WithdrawRequest::where('user_id',$client)->OrderBy('created_at','desc')->get();
            $wallte = Wallet::where('user_id',$client)->first();

            return view('clientside.withdraw',compact('pageTitle','wallte','withdrawrequest'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id ?? $request->user_id;

        $withdrawrequest_exist = WithdrawRequest::where('user_id', $data['user_id'])->where('status', 'requested')->exists();
        if($withdrawrequest_exist) {
            $message = __('message.already_withdrawrequest');
            return response()->json(['status' => true,  'message' => $message,'event' => 'refresh']);
        }
        $withdrawrequest = WithdrawRequest::create($data);

        $message = __('message.save_form',['form' => __('message.withdrawrequest')]);
        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }
        return response()->json(['status' => true,  'message' => $message,'event' => 'refresh']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('withdrawrequest-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.order')]);
        $user = User::findOrFail($id);
        $data = UserBankAccount::where('user_id',$id)->first();
        return view('withdrawrequest.show', compact('data','pageTitle','user'));
    }

    public function withdrawExcel(Request $request,$fileType)
    {
        $selectedColumns = $request->input('columns', []);
        $filename =  "withdrawrequest-pending.{$fileType}" ;
        $export = new WithdrawRequestExport($request, $selectedColumns);
        switch ($fileType) {
            case 'xlsx':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
            case 'xls':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLS);
            case 'ods':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::ODS);
            case 'csv':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
            case 'html':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::HTML);
            default:
                return back()->withErrors(['file_type' => 'Unsupported file type selected.']);
        }
    }
    public function withdrawrequestmodel()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.withdrawrequest')]);
        return view('withdrawrequest.withdrawrequestexportmodel', compact('pageTitle'));
    }
    public function withdrawApprovedExcel(Request $request,$fileType)
    {
        $selectedColumns = $request->input('columns', []);
        $filename =  "withdrawrequest-approved.{$fileType}" ;
        $export = new WithdrawRequestapprovedExport($request, $selectedColumns);
        switch ($fileType) {
            case 'xlsx':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
            case 'xls':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLS);
            case 'ods':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::ODS);
            case 'csv':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
            case 'html':
                return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::HTML);
            default:
                return back()->withErrors(['file_type' => 'Unsupported file type selected.']);
        }
    }
    public function withdrawapprovedmodel()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.withdrawrequestapproved')]);
        return view('withdrawrequest.withdrawapprovedexportmodel', compact('pageTitle'));
    }
    public function withdrawhestory($id)
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.withdraw_detail')]);
        return view('withdrawrequest.model', compact('pageTitle','id'));

    }

    public function withdrawdetailstore(Request $request)
    {
        $data = request()->all();

        $withdraw = WithdrawRequest::find($data['withdrawrequest_id']);
        $withdraw->status = 'completed';
        $withdraw->datetime = now();
        $withdraw->save();  
        $withdrawdetail = WithdrawDetail::create($data);
        uploadMediaFile($withdrawdetail, $request->withdrawimage, 'withdrawimage');

        $message = __('message.save_form',[ 'form' => __('message.withdraw_detail') ] );

         $notification_data['message'] = __('message.withdrewrequest_completed');
         $user = User::find($withdraw->user_id);
         if($user != null){
            $user->notify(new OrderNotification($notification_data));
         }

        return redirect()->back()->withsuccess($message);
    }

    public function withdrawdetailedit($id)
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.withdraw_detail')]);
        $data = WithdrawDetail::where('withdrawrequest_id', $id)->first();
        if (!$data) {

            return redirect()->route('withdraw-history');
        }
        return view('withdrawrequest.model', compact('data','id','pageTitle'));
    }
}
