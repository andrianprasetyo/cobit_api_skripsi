<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CobitHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Repository\RepositoryResource;
use App\Models\MediaRepository;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaRepositoryController extends Controller
{
    use JsonResponse;

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $search = $request->search;
        $type = $request->type;
        $assesment_id = $request->assesment_id;

        $list = MediaRepository::with(['author','assesment']);
        if ($request->filled('search')) {
            $list->where('deskripsi', 'ilike', '%' . $search . '%');
            $list->orWhere('docs','ilike', '%' . $search . '%');
        }

        if($request->filled('type'))
        {
            $list->whereJsonContains('docs->ext',$type);
        }

        if ($request->filled('assesment_id'))
        {
            $list->where('assesment_id',$assesment_id);
        }

        $list->orderBy($sortBy, $sortType);
        $data = $this->paging($list, $limit, $page, RepositoryResource::class);
        return $this->successResponse($data);
    }

    public function add(Request $request)
    {
        $data=null;
        $docs = $request->file('docs');
        $assesment_id=$request->assesment_id;
        $path = config('filesystems.path.repository') .$assesment_id.'/';
        $deskripsi=$request->deskripsi;
        if(is_array($docs))
        {
            $media_payload=[];
            for ($i=0; $i <count($docs) ; $i++) {
                $filename = date('Ymdhis') . '-' . $assesment_id . '-' .$docs[$i]->hashName();
                $docs[$i]->storeAs($path, $filename);
                $filedocs = CobitHelper::Media($filename, $path, $docs[$i]);
                $media_payload[]=array(
                    'assesment_id'=>$assesment_id,
                    'upload_by'=>Auth::user()->id,
                    'deskripsi'=>isset($deskripsi[$i]) && $deskripsi[$i] !=''?$deskripsi[$i]: $filedocs['originalname'],
                    'docs'=>json_encode($filedocs),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                );
            }

            MediaRepository::insert($media_payload);
            $data=$media_payload;
        }
        else
        {
            if($request->hasFile('docs'))
            {
                $filename = date('Ymdhis').'-'.$assesment_id.'-'.$docs->hashName();
                $docs->storeAs($path, $filename);
                $filedocs = CobitHelper::Media($filename, $path, $docs);
                $doc=new MediaRepository();
                $doc->assesment_id = $assesment_id;
                $doc->docs = $filedocs;

                $doc->deskripsi = $filedocs['originalname'];
                if($request->filled('deskripsi'))
                {
                    $doc->deskripsi=$deskripsi;
                }
                $doc->upload_by=Auth::user()->id;
                $doc->save();

                $data=$doc;
            }
        }

        return $this->successResponse($data);
    }

    public function detailByID($id)
    {
        $data = MediaRepository::find($id);
        return $this->successResponse(new RepositoryResource($data));
    }

    public function remove($id)
    {
        $data=MediaRepository::find($id);
        if(!$data)
        {
            return $this->errorResponse('Data tidak tersedia',404);
        }

        if($data->docs != null && Storage::exists($data->docs['path']))
        {
            Storage::delete($data->docs['path']);
        }

        $data->delete();
        return $this->successResponse();
    }
}
