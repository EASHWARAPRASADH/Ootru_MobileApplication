<?php

namespace App\Http\Controllers;

use App\DataTables\EmergencyDataTable;
use App\Models\Emergency;
use App\Models\SMSTemplate;
use App\Models\SosNumber;
use App\Models\User;
use App\Services\SmsService;
use App\Traits\OrderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyController extends Controller
{
    use OrderTrait;

    public function index(EmergencyDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.emergency')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = null;
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title = "document-checked" class="float-left btn btn-sm ">'.__('message.delete_selected').'</button>' : '';

        return $dataTable->render('global.datatable', compact('assets', 'pageTitle', 'button', 'auth_user',
            'multi_checkbox_delete'));
    }

    public function emergencyList()
    {
        $auth = auth()->user();
        $list = Emergency::where(['delivery_man_id' => $auth->id, 'status' => 0])->get()->toArray();
        if (empty($list)) {
            $message = __('message.no_record_found');
        } else {
            $message = __('message.success');
        }

        return response()->json(['message' => $message, 'data' => $list, 'status' => true]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $auth = auth()->user()->id;
    //     $data = $request->all();
    //     $data['datetime'] =  now();
    //     $data['status'] =  0;
    //     $data['delivery_man_id'] =  auth()->user()->id;
    //     $staticData = Emergency::create($data);

    //         $message =  __('message.sent_emergency') ;

    //       if(SettingData('emergency','alert_notitfication') == 1){
    //           $this->playAlertSound();
    //       }
    //     if ($request->is('api/*')) {
    //         return json_message_response($message);

    //     }
    //     return redirect()->route('emergency.index')->withSuccess($message);
    // }
    public function store(Request $request)
    {
        $auth = auth()->user();
        $existingEmergency = Emergency::where('delivery_man_id', $auth->id)->where('status', 0)->first();

        if ($existingEmergency) {
            $message = __('message.pending_emergency_exists');

            if ($request->is('api/*')) {
                return response()->json(['message' => $message, 'id' => -1, 'status' => false]);
            }
        }
        $data = $request->all();
        $data['datetime'] = now();
        $data['status'] = 0;
        $data['delivery_man_id'] = $auth->id;

        $emergency = Emergency::create($data);
        $message = __('message.sent_emergency');

        if (SettingData('emergency', 'alert_notitfication') == 1) {
            $this->playAlertSound();
        }

        $smsCheck = (int) (appSettingData('is_sms_order')->is_sms_order ?? 0);
        if ($smsCheck === 1) {
            try {
                $sosNumbers = SosNumber::where('delivery_man_id', $auth->id)->get();
                $template = SMSTemplate::where('type', 'emergency')->first();
                Log::info('SMS Template: '.json_encode($template));

                if ($template) {
                    $locationPayload = [
                        'lat' => $auth->latitude,
                        'lng' => $auth->longitude,
                    ];

                    $encryptedPayload = encrypt($locationPayload);
                    $url = env('APP_URL').'/map?deliveryMan='.$this->shortURL($encryptedPayload);

                    $smsMessage = str_replace(
                        ['{{delivery_man}}', '{{lat}}', '{{lng}}', '{{url}}'],
                        [$auth->name, $auth->latitude, $auth->longitude, $url],
                        strip_tags($template->sms_description)
                    );

                    if (!str_contains($template->sms_description, '{{url}}')) {
                        $smsMessage .= "\nLocation: ".$url;
                    }

                    $smsService = app(SmsService::class);
                    foreach ($sosNumbers as $sos) {
                        if (!empty($sos->contact_number)) {
                            $smsService->sendSMS($sos->contact_number, $smsMessage);
                            sleep(1);
                        }
                    }
                } else {
                }
            } catch (\Exception $e) {
            }
        }

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'id' => $emergency->id, 'status' => true]);
        }

        return redirect()->route('emergency.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pageTitle = __('message.delivery_man_live_tracking');
        $assets = ['location'];
        $emergency = Emergency::with('deliveryMan')->findOrFail($id);
        $latitude = $emergency->deliveryMan?->latitude;
        $longitude = $emergency->deliveryMan?->longitude;
        $status = $emergency->status;

        return view('emergency.map', compact('latitude', 'longitude', 'id', 'assets', 'status', 'pageTitle', 'emergency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $emergency = Emergency::find($id);

        return view('emergency.show', compact('id', 'emergency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request ,$id)
    {
        $emergency = Emergency::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.emergency')]);
        if ($emergency == null) {
            return json_message_response($message);
        }

        if ($request->emergency_resolved) {
            $request['status'] = 2;
        }
        $emergency->update($request->all());

        $message = __('message.update_form', ['form' => __('message.emergency')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }

        return redirect()->back()->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }

    public function showMap(Request $request)
    {
        $shortToken = $request->get('deliveryMan');

        $encryptedPayload = Cache::get('map_token_'.$shortToken);

        if (!$encryptedPayload) {
            abort(404, 'Link expired or invalid.');
        }

        $location = decrypt($encryptedPayload);

        return view('emergency.map-view', [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ]);
    }

    public function shortURL($payload)
    {
        $token = Str::random(8);

        Cache::put('map_token_'.$token, $payload, now()->addDay());

        return $token;
    }

    public function trackLocation($id)
    {
        $emergency = Emergency::find($id);

        return response()->json([
            'latitude' => $emergency->deliveryMan->latitude,
            'longitude' => $emergency->deliveryMan->longitude,
        ]);
    }

    public function sosStore(Request $request)
    {
        $request->validate([
            'contact_number' => 'required',
        ]);
        $data = $request->all();
        $data['delivery_man_id'] = auth()->user()->id;
        $sos = SosNumber::create($data);
        $message = __('message.save_form', ['form' => __('message.emergency_number')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
    }

    public function sosList()
    {
        $auth = auth()->user();
        $sos = SosNumber::where('delivery_man_id', $auth->id)->get();

        return response()->json([
            'status' => true,
            'data' => $sos,
        ]);
    }

    public function sosDestroy($id)
    {
        $country = SosNumber::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.contact_number')]);

        if ($country != '') {
            $country->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.contact_number')]);
        }

        if (request()->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
