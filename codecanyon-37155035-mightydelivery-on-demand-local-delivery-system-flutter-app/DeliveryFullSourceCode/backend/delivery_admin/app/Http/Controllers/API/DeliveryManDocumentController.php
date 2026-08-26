<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryManDocument;
use App\Http\Resources\DeliveryManDocumentResource;

class DeliveryManDocumentController extends Controller
{
    public function getList(Request $request)
    {
        $delivery_man_document = DeliveryManDocument::myDocument();

        $delivery_man_document->when(request('delivery_man_id'), function ($q) {
            return $q->where('delivery_man_id', request('delivery_man_id'));
        });

        if ($request->has('is_verified') && isset($request->is_verified)) {
            $delivery_man_document = $delivery_man_document->where('is_verified', request('is_verified'));
        }

        $per_page = config('constant.PER_PAGE_LIMIT');

        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $delivery_man_document->count();
            }
        }

        $delivery_man_document = $delivery_man_document->orderBy('id', 'desc')->paginate($per_page);
        $items = DeliveryManDocumentResource::collection($delivery_man_document);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }
    public function multipleDeleteRecords(Request $request)
    {
        $multi_ids = $request->ids;
        $message = __('message.msg_fail_to_delete', ['item' => __('message.delivery_man_document')]);

        foreach ($multi_ids as $id) {
            $delivery_man_document = DeliveryManDocument::withTrashed()->where('id', $id)->first();
            if ($delivery_man_document) {
                if ($delivery_man_document->deleted_at != null) {
                    $delivery_man_document->forceDelete();
                } else {
                    $delivery_man_document->delete();
                }
                $message = __('message.msg_deleted', ['name' => __('message.delivery_man_document')]);
            }
        }

        return json_custom_response(['message' => $message, 'status' => true]);
    }
}
