<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteSection;
use App\Models\WebsiteSectionTitle;
use App\Http\Requests\WebSiteSectionRequest;
use App\Models\FrontendData;
use App\Models\Setting;

class WebSiteSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle =  __('message.section');
        $sections = WebsiteSection::with('websitesectiontitles')->get();
        $data = FrontendData::where('type','app_overview')->get();

        $app_overview = config('constant.app_overview');
        
        foreach ($app_overview as $key => $value) {
            if( in_array($key,['title','subtitle'])){
                $app_overview[$key] = Setting::where('type','app_overview')->where('key',$key)->pluck('value')->first();
            }else{
                $app_overview[$key] = Setting::where('type','app_overview')->where('key',$key)->first();
            }
        }
        return view('websitesection.section.main', compact('pageTitle','app_overview','data','sections'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.section') ]);
        $data = new WebsiteSection();
        $titles = $data->websitesectiontitles->pluck('title')->toArray() ?? [];
        return view('websitesection.section.form', compact('pageTitle', 'data','titles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebSiteSectionRequest $request)
    {
        foreach ($request->sections as $section) {
        
            $newSection = WebsiteSection::create([
                'title' => $section['title'],
                'subtitle' => $section['subtitle'],
            ]);

            if (isset($section['section_image']) && $section['section_image'] instanceof \Illuminate\Http\UploadedFile) {
                uploadMediaFile($newSection, $section['section_image'], 'section_image');
            }

            if (isset($section['titles']) && is_array($section['titles'])) {
                $filteredTitles = array_filter($section['titles'], function($title) {
                    return !empty(trim($title));
                });
    
                foreach ($filteredTitles as $title) {
                    WebsiteSectionTitle::create([
                        'section_id' => $newSection->id,
                        'title' => trim($title),
                    ]);
                }
            }
           
        }
        $message = __('message.save_form', ['form' => __('message.section')]);
        return redirect()->route('app-overview.index')->withSuccess($message);

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
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.section') ]);
        $data = WebsiteSection::with('websitesectiontitles')->findOrFail($id);
        $titles = $data->websitesectiontitles->pluck('title')->toArray() ?? [];
        return view('websitesection.section.form', compact('data','pageTitle','id','titles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WebSiteSectionRequest $request, $id)
    {
        $section = WebsiteSection::findOrFail($id);
        
        $section->update([
            'title' => $request->sections[0]['title'],
            'subtitle' => $request->sections[0]['subtitle'],
        ]);

        if (isset($request->sections[0]['section_image']) && $request->sections[0]['section_image'] instanceof \Illuminate\Http\UploadedFile) {
            $section->clearMediaCollection('section_image');
            $section->addMedia($request->sections[0]['section_image'])->toMediaCollection('section_image');
        }

        $section->websitesectiontitles()->delete();

        if (isset($request->sections[0]['titles']) && is_array($request->sections[0]['titles'])) {
            $filteredTitles = array_filter($request->sections[0]['titles'], function($title) {
                return !empty(trim($title));
            });

            foreach ($filteredTitles as $title) {
                WebsiteSectionTitle::create([
                    'section_id' => $section->id,
                    'title' => trim($title),
                ]);
            }
        }
        
        $message = __('message.update_form',['form' => __('message.section')]);
        return redirect()->route('app-overview.index')->withSuccess($message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $section = WebsiteSection::findOrFail($id);
        if($section != '') {
            $section->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.section')]);
        }
        $section->delete();
        return redirect()->back()->with($status,$message);
    }

    public function helpAppOverview(){

        $pageTitle = __('message.app_overview');
        return view('websitesection.section.help',compact([ 'pageTitle' ]));  
    }
}
 
