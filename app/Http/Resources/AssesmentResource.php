<?php

namespace App\Http\Resources;

use App\Models\AssesmentDocs;
use App\Models\UserAssesment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssesmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assesment_pic =null;
        if(isset($this->pic->id)){

            $assesment_pic = UserAssesment::where('assesment_id', $this->id)->where('users_id', $this->pic->id)->first();
        }
        $docs = null;
        // if($this->docs){
        //     $docs=AssesmentDocs::where('assesment_id',$this->id)->orderByDesc('created_at')->first();
        // }
        // $tahun=Carbon::parse($this->tahun);
        return [
            'id'=>$this->id,
            'nama'=>$this->nama,
            'organisasi_id'=>$this->organisasi_id,
            'status'=>$this->status,
            'deskripsi'=>$this->deskripsi,
            'created_at'=>$this->created_at,
            // 'tahun'=> $tahun->format('Y-m'),
            'start_date' => $this->start_date,
            'end_date'=>$this->end_date,
            'users_id' => $this->users_id,
            'organisasi'=>$this->organisasi,
            'pic' => $this->pic,
            'start_date_quisioner' => $this->start_date_quisioner,
            'end_date_quisioner' => $this->end_date_quisioner,
            'minimum_target' => $this->minimum_target,
            'assesment_user'=>$assesment_pic,
            'users_count'=>$this->users_count,
            'allpic' => $this->allpic,
            // 'docs' => $this->docs,
            // 'docs' => $docs,
        ];
    }
}
