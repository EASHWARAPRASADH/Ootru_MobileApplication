<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profofpictures;
use App\Http\Resources\ProfofpicturesResource;

class ProfofPicturesController extends Controller
{
    public function profOfpictureList(Request $request)
    {
        $profpicture = Profofpictures::query();

        $profpicture->when(request('order_id'), function ($q) {
            return $q->where('order_id', request('order_id'));
        });
        $profpicture->when(request('type'), function ($q) {
            return $q->where('type', request('type'));
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $profpicture->count();
            }
        }

        $profpicture = $profpicture->orderBy('id','asc')->paginate($per_page);
        $items = ProfofpicturesResource::collection($profpicture);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function profOfpictureSave(Request $request)
    {
        $data = $request->all();
        if ($request->is('api/*')) {
            $profpicture = Profofpictures::create($data);
            if ($request->hasFile('prof_file')) {
                $profpicture->clearMediaCollection('prof_file');
    
                foreach ($request->file('prof_file') as $image) {
                    $profpicture->addMedia($image)->toMediaCollection('prof_file');
                }
            }
            $message = __('message.save_form', ['form' => __('message.profofpicture')]);
            if ($request->is('api/*')) {
                return json_message_response($message);
            }
            return json_custom_response($message);
        }
    }
}
