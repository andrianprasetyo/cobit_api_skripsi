<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait JsonResponse
{
    protected function successResponse($data=null, $message = 'Ok', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = null, $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    protected function paging($list,$limit=null,$offset=null)
    {

        $total = $list->count();

        if($limit != null || $offset !=null)
        {
            $page=($offset * $limit) - $limit;
            $list->limit($limit);
            $list->skip($page);
            // $list->skip($offset);

            $meta['per_page'] = (int) $limit;
            $meta['total_page'] = ceil($total / $limit);
            // $meta['current_page'] = ceil($offset / $limit) + 1;
            $meta['current_page'] = (int)$offset;
        }

        $data['list'] = $list->get();
        $meta['total'] = $total;
        $data['meta'] = $meta;

        return $data;
    }
}
