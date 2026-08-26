<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claims;
use App\DataTables\ClaimsDataTable;
use App\Http\Resources\ClaimsHistoryResource;
use App\Http\Resources\ClaimsResource;
use App\Models\ClaimsHistory;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use App\Notifications\CustomerSupportNotification;

class ClaimsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClaimsDataTable $dataTable)
    {
        if (!auth()->user()->can('claims-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title', ['form' => __('message.claims_management')]);
        $auth_user = authSession();
        $assets = ['datatable'];


        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();
    
        $traking_no_exits = Order::where('milisecond', $request->traking_no)->exists();
        if (!$traking_no_exits) {
            return json_custom_response([
                'message' => __('message.invalid_traking_id'),
                'success' => false
            ], 400);
        }else{

            $claims = Claims::create($data);
            $claims->isClaimed = 1 ;
            $claims->save();
    
            if ($request->hasFile('attachment_file')) {
                $claims->clearMediaCollection('attachment_file');
    
                foreach ($request->file('attachment_file') as $image) {
                    $claims->addMedia($image)->toMediaCollection('attachment_file');
                }
            }
        }

        $notification_data = [
            'id'   => $claims->id,
            'type'      => __('message.claims'),
            'subject'     => __('message.claims', ['id' => $claims->id]),
            'message'   => __('message.claims_has_been_submitted', [
                'claim_id'     => $claims->id,
                'prof_value'   => $claims->prof_value,
               'client_name' => $claims->user ? $claims->user->name : 'Unknown Client',
            ]),
        ];
        $admins = User::admin()->get();
        foreach ($admins as $admin) {
            $admin->notify(new CustomerSupportNotification($notification_data));
        }


        $message = __('message.save_form', ['form' => __('message.claims')]);
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('claims.index')->withSuccess($message);
    }


    public function claimsmodel($id)
    {
        $data = ClaimsHistory::where('claim_id',$id)->first();
        $pageTitle = __('message.claims');

        return view('claims.claimsmodel', compact(['pageTitle','id','data']));
    }
    public function approvedStatus(Request $request,$id)
    {
        $status = $request->input('status');
        $claims = Claims::find($id);
        if ($claims == null) {
            $message = __('message.not_found_entry', ['name' => __('message.claims')]);
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withErrors($message);
        }   
        if($status == 1){
            $claims->status = 'approved';
            $message = __('message.status_approved');
            $claims->save();
        }else{
            $claims->status = 'reject';
            $claims->save();
            $message = __('message.status_reject');
        }
        
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->back()->withSuccess($message);
    }

    public function claimhistorySave(Request $request)
    {
        $data = request()->all();
        $history = Claims::find($data['claim_id'] ?? '');

        if ($history) {
                $claimshistory = ClaimsHistory::create([
                    'claim_id' => $history->id,
                    'amount' => $data['amount'],
                    'description' => $data['description'],
                ]);
                $history->update(['status' => 'close']);
                if ($request->hasFile('attachment_resolve_file')) {
                    $claimshistory->clearMediaCollection('attachment_resolve_file');
        
                    foreach ($request->file('attachment_resolve_file') as $image) {
                        $claimshistory->addMedia($image)->toMediaCollection('attachment_resolve_file');
                    }
                }
            $message = __('message.save_form',[ 'form' => __('message.claimshistory') ] );
            if (request()->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withSuccess($message);
        }
        return redirect()->back()->withError(__('message.claim_not_found'));
    }

   public function statusdetail(Request $request)
   {
       $data = $request->all();
       $claim_id = $data['claim_id'] ?? $data['id'];
       $claim = Claims::find($claim_id);

       if (!$claim) {
           $message = __('message.not_found_entry', ['name' => __('message.claims')]);

           if ($request->is('api/*')) {
               return json_message_response($message);
           }

           return redirect()->back()->withErrors($message);
       }

       if (isset($data['claim_id'])) {
           $claimHistory = ClaimsHistory::create([
               'claim_id' => $claim->id,
               'amount' => $data['amount'],
               'description' => $data['description'],
           ]);

           $claim->update(['status' => 'close']);

           if ($request->hasFile('attachment_resolve_file')) {
               $claimHistory->clearMediaCollection('attachment_resolve_file');
               foreach ($request->file('attachment_resolve_file') as $image) {
                   $claimHistory->addMedia($image)->toMediaCollection('attachment_resolve_file');
               }
           }

           $message = __('message.save_form', ['form' => __('message.claimshistory')]);

           if ($request->is('api/*')) {
               return json_message_response($message);
           }

           return redirect()->back()->withSuccess($message);
       } else {
           $claim->status = $request->status == 1 ? 'approved' : 'reject';
           $claim->save();

           $message = __('message.status_updated');

           if ($request->is('api/*')) {
               return json_message_response($message);
           }

           return redirect()->back()->withSuccess($message);
       }
   }


   public function show($id)
   {
       $pageTitle = __('message.add_form_title',[ 'form' => __('message.claims')]);
       $data = Claims::findOrFail($id);
       $mediaItems = $data->getMedia('attachment_file');
       return view('claims.show', compact('data','mediaItems','id'));
   }
   public function closeview($id)
   {
      $pageTitle = __('message.add_form_title',[ 'form' => __('message.claimshistory')]);
      $data = Claims::findOrFail($id);
      $mediaItems = $data->getMedia('attachment_file');
      return view('claims.show', compact('data','mediaItems','id'));
   }

   public function getList(Request $request)
   {
        $user = auth()->user();
        $claims = $user->hasRole('admin') ? Claims::query() : Claims::myClaims();

        if( $request->has('status') && isset($request->status) ) {
            $claims = $claims->where('status',request('status'));
        }
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $claims->count();
            }
        }

        $claims = $claims->orderBy('id', 'desc')->paginate($per_page);
        $items = ClaimsResource::collection($claims);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];
        
        return json_custom_response($response);
   }
   public function claimhistorygetList(Request $request)
   {
        $user = auth()->user();
        $claimshistory = $user->hasRole('admin') ? ClaimsHistory::query() : ClaimsHistory::myClaimshistory();

        if( $request->has('claim_id') && isset($request->status) ) {
            $claimshistory = $claimshistory->where('claim_id',request('claim_id'));
        }
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $claimshistory->count();
            }
        }

        $claimshistory = $claimshistory->orderBy('id', 'desc')->paginate($per_page);
        $items = ClaimsHistoryResource::collection($claimshistory);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];
        
        return json_custom_response($response);
   }
}
