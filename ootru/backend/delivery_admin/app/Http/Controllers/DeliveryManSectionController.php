<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryManSectionRequest;
use App\Models\DeliveryManSection;
use App\Models\DeliveryManSectionTitles;
use App\Models\FrontendData;
use App\Models\Setting;

class DeliveryManSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle =  __('message.delivery_man_section');
        $sections = DeliveryManSection::with('deliverymansectiontitles')->get();
        $data = FrontendData::where('type','delivery_man_section')->get();

        $delivery_man_section = config('constant.delivery_man_section');
        
        foreach ($delivery_man_section as $key => $value) {
            if( in_array($key,['title','subtitle'])){
                $delivery_man_section[$key] = Setting::where('type','delivery_man_section')->where('key',$key)->pluck('value')->first();
            }else{
                $delivery_man_section[$key] = Setting::where('type','delivery_man_section')->where('key',$key)->first();
            }
        }
        return view('deliverymansection.main', compact('pageTitle','delivery_man_section','data','sections'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.section') ]);
        $data = new DeliveryManSection();
        $titles = $data->deliverymansectiontitles->pluck('title')->toArray() ?? [];
        return view('deliverymansection.form', compact('pageTitle', 'data','titles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryManSectionRequest $request)
    {
        foreach ($request->sections as $section) {
        
            $newSection = DeliveryManSection::create([
                'title' => $section['title'],
            ]);

            if (isset($section['delivery_man_section_image']) && $section['delivery_man_section_image'] instanceof \Illuminate\Http\UploadedFile) {
                uploadMediaFile($newSection, $section['delivery_man_section_image'], 'delivery_man_section_image');
            }

            if (isset($section['titles']) && is_array($section['titles'])) {
                $filteredTitles = array_filter($section['titles'], function($title) {
                    return !empty(trim($title));
                });
    
                foreach ($filteredTitles as $title) {
                    DeliveryManSectionTitles::create([
                        'section_id' => $newSection->id,
                        'title' => trim($title),
                    ]);
                }
            }
           
        }
        $message = __('message.save_form', ['form' => __('message.section')]);
        return redirect()->route('delivery-man-section.index')->withSuccess($message);

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
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.delivery_man_section') ]);
        $data = DeliveryManSection::with('deliverymansectiontitles')->findOrFail($id);
        $titles = $data->deliverymansectiontitles->pluck('title')->toArray() ?? [];
        return view('deliverymansection.form', compact('data','pageTitle','id','titles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryManSectionRequest $request, $id)
    {
        $section = DeliveryManSection::findOrFail($id);
        
        $section->update([
            'title' => $request->sections[0]['title'],
        ]);

        if (isset($request->sections[0]['delivery_man_section_image']) && $request->sections[0]['delivery_man_section_image'] instanceof \Illuminate\Http\UploadedFile) {
            $section->clearMediaCollection('delivery_man_section_image');
            $section->addMedia($request->sections[0]['delivery_man_section_image'])->toMediaCollection('delivery_man_section_image');
        }

        $section->deliverymansectiontitles()->delete();

        if (isset($request->sections[0]['titles']) && is_array($request->sections[0]['titles'])) {
            $filteredTitles = array_filter($request->sections[0]['titles'], function($title) {
                return !empty(trim($title));
            });

            foreach ($filteredTitles as $title) {
                DeliveryManSectionTitles::create([
                    'section_id' => $section->id,
                    'title' => trim($title),
                ]);
            }
        }
        
        $message = __('message.update_form',['form' => __('message.delivery_man_section')]);
        return redirect()->route('delivery-man-section.index')->withSuccess($message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $section = DeliveryManSection::findOrFail($id);
        if($section != '') {
            $section->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.delivery_man_section')]);
        }
        $section->delete();
        return redirect()->back()->with($status,$message);
    }

    public function helpDeliveryManSection(){

        $pageTitle = __('message.delivery_man_section');
        return view('deliverymansection.help',compact([ 'pageTitle' ]));  
    }
}
 
