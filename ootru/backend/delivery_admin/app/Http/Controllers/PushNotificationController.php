<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNotification;
use App\DataTables\PushNotificationDataTable;
use App\Models\User;
use App\Notifications\CommonNotification;
use App\Notifications\DatabaseNotification;
use App\Models\Notification;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PushNotificationDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.pushnotification')]);
        $auth_user = authSession();
        if (!auth()->user()->can('push notification-list')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $assets = ['data-table'];

        $button = $auth_user->can('push notification-add') ? '<a href="' . route('pushnotification.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.send_pushnotification') . '</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'button'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('push notification-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.set_form_title', ['form' => __('message.pushnotification')]);
        $relation = [
            'client' => User::where('user_type', 'client')->where('status', '1')->get()->pluck('name', 'id'),
            'delivery_man' => User::where('user_type', 'delivery_man')->where('status', '1')->get()->pluck('name', 'id'),
        ];
        return view('push_notification.form', compact('pageTitle') + $relation);
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

        $clientCount = 0;
        if (request()->is('api/*')) {
            $data['client'] = json_decode($request->client) ?? [];
            $data['delivery_man'] = json_decode($request->delivery_man) ?? [];
        }

        if (isset($data['client']) && isset($data['delivery_man'])) {
            $clientCount = count($data['client']) + count($data['delivery_man']);
        } elseif (isset($data['client'])) {
            $clientCount = count($data['client']);
        } elseif (isset($data['delivery_man'])) {
            $clientCount = count($data['delivery_man']);
        }

        $pushnotification = PushNotification::create($data);
        $pushnotification->notification_count = $clientCount;
        $pushnotification->save();
        $notify_type = $request->notify_type ?? null;
        
        uploadMediaFile($pushnotification, $request->notification_image, 'notification_image');

        if (request()->is('api/*')) {
            $message = __('message.save_form', ['form' => __('message.pushnotification')]);
            return json_message_response($message);
        }
        $notification_data = [
            'id' => $pushnotification->id,
            'push_notification_id' => $pushnotification->id,
            'type' => 'push_notification',
            'subject' => $pushnotification->title,
            'message' => $pushnotification->message,
        ];
        // Log::debug('Notification Data:', $notification_data);
        if (getMediaFileExit($pushnotification, 'notification_image')) {
            $notification_data['image'] = getSingleMedia($pushnotification, 'notification_image');
        } else {
            $notification_data['image'] = null;
        }
        if ($request->has('client')) {
            User::whereIn('id', $request->client)->chunk(20, function ($userdata) use ($notification_data) {
                foreach ($userdata as $user) {
                    $user->notify(new CommonNotification($notification_data['type'], $notification_data));
                    $user->notify(new DatabaseNotification($notification_data));
                }
            });
        }

        if ($request->has('delivery_man')) {
            User::whereIn('id', $request->delivery_man)->chunk(20, function ($userdata) use ($notification_data) {
                foreach ($userdata as $user) {
                    $user->notify(new CommonNotification($notification_data['type'], $notification_data));
                    $user->notify(new DatabaseNotification($notification_data));
                }
            });
        }

        return redirect()->route('pushnotification.index')->withSuccess(__('message.'.($notify_type != 'resend' ? 'pushnotification_send_success' : 'pushnotification_resend_success')));   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = PushNotification::findOrFail($id);
        $pageTitle = __('message.resend_pushnotification');
        $relation = [
            'client' => User::where('user_type', 'client')->where('status', '1')->get()->pluck('name', 'id'),
            'delivery_man' => User::where('user_type', 'delivery_man')->where('status', '1')->get()->pluck('name', 'id'),
        ];

        return view('push_notification.form', compact('data','id','pageTitle')+$relation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('push notification-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->is('api/*')){
                return response()->json(['status' => true, 'message' => $message ]);
            }
            if(request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('pushnotification.index')->withErrors($message);
        }
        $pushnotification = PushNotification::findOrFail($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.pushnotification')]);

        if ($pushnotification != '') {
            Notification::whereJsonContains('data->push_notification_id', $id);
            $pushnotification->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.pushnotification')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }

        return redirect()->back()->with($status, $message);
    }
    public function allnotification()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.pushnotification')]);
        $relation = [
            'client' => User::where('user_type', 'client')->where('status', '1')->get()->pluck('name', 'id'),
            'delivery_man' => User::where('user_type', 'delivery_man')->where('status', '1')->get()->pluck('name', 'id'),
        ];


       return view('push_notification.allnotification', compact('pageTitle') + $relation);

    }
}
