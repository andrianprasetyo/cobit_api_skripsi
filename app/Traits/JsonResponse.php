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

    protected function paging($list,$limit,$offset)
    {

        $total = $list->count();

        // $offset = $offset == 1 ? 0 : $total - $limit + 1;
        //$offset = $offset == 1 ? 0 : $total - $limit + 1;
        $list->limit($limit);
        $list->skip(($offset * $limit) - $limit);

        $data['rows'] = $list->get();
        $meta['total'] = $total;
        $meta['total_page'] = ceil($total / $limit);
        $meta['per_page'] = (int) $limit;
        $meta['current_page'] = ceil($offset / $limit) + 1;
        $data['meta'] = $meta;

        return $data;
    }
}
