<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PagesResource;
use App\Models\Pages;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{
    public function getList(Request $request)
    {
        $user = Auth::user();
        if($user->hasRole('admin')){
            $params = $request->search;
            $pages = Pages::where('title', 'LIKE', '%' . $params . '%');
        }else{
            $pages = Pages::query()->whereStatus(1);
        }
        $pages->when(request('id'), function ($q) {
            return $q->where('id', 'LIKE', '%' . request('id') . '%');
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $pages->count();
            }
        }

        $pages = $pages->orderBy('id','desc')->paginate($per_page);
        $items = PagesResource::collection($pages);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function getDetail(Request $request){

        $id = $request->id;
        $page = Pages::where('id', $id)->first();
        $page_detail = new PagesResource($page);
        $message = __('message.not_found_entry', ['name' => __('message.pages')]);
        if ($page == null) {
            return response()->json(['status' => true, 'message' => $message ]);
        }
        $response = [
            'data' => $page_detail,
        ];

        return json_custom_response($response);
    }

}
