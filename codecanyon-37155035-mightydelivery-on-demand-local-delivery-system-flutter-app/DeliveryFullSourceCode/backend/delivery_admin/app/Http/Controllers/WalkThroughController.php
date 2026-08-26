<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrontendData;
use App\Models\Setting;
use App\Http\Requests\WalkThroughRequest;

class WalkThroughController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle =  __('message.walkthrough');
        $count = FrontendData::where('type','walkthrough')->count();

        $data = FrontendData::where('type','walkthrough')->get();

        return view('walkthrough.main', compact('pageTitle','data','count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.walkthrough_section')]);

        return view('walkthrough.model', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WalkThroughRequest $request)
    {
        $data = $request->all();

        $result = FrontendData::Create($data);

        uploadMediaFile($result, $request->frontend_data_image, 'frontend_data_image');

        $type = $result->type ? __('message.'.$result->type) : __('message.record');

        $message = __('message.update_form',[ 'form' => $type ] );
		if($result->wasRecentlyCreated){
			$message = __('message.save_form',[ 'form' => $type ] );
		}

        if($request->is('api/*')) {
            return json_message_response($message);
		}
        return response()->json(['status' => true, 'message' => $message,'event' =>  'refresh']);

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
        $pageTitle = __('message.update_form_title', ['form' => __('message.walkthrough_section')]);
        $data = FrontendData::findOrFail($id);
        return view('walkthrough.model', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WalkThroughRequest $request, $id)
    {
        $frontenddata = FrontendData::find($id);
        uploadMediaFile($frontenddata,$request->frontend_data_image, 'frontend_data_image');
        $message = __('message.not_found_entry', ['name' => __('message.frontenddata')]);
        if($frontenddata == null) {
            return json_custom_response(['status' => false, 'message' => $message ],400);
        }
        $message = __('message.update_form',[ 'form' => __('message.frontenddata') ] );
        $frontenddata->fill($request->all())->update();

        if($frontenddata != '') {
            $frontenddata->update();
            $status = 'success';
            $message = __('message.update_form', ['form' => __('message.frontenddata')]);
        }
        if(request()->is('api/*')){
            return json_message_response($message);
        }
        if(auth()->check()){
            return response()->json(['status' => true, 'event' =>  'refresh', 'message'=> $message]);
        }
        return response()->json(['status' => true,  'message'=> $message]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
    public function helpwalkthrough(){

        $pageTitle = __('message.walkthrough');
        return view('walkthrough.help',compact([ 'pageTitle' ]));  
    }
}
