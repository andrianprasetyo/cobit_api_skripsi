<?php
namespace App\Traits;

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
            $list->limit($limit);
            $list->skip(($offset * $limit) - $limit);

            $meta['per_page'] = (int) $limit;
            $meta['total_page'] = ceil($total / $limit);
            $meta['current_page'] = ceil($offset / $limit) + 1;
        }

        $data['list'] = $list->get();
        $meta['total'] = $total;
        $data['meta'] = $meta;

        return $data;
    }
}
