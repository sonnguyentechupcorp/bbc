<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $include = request()->get('include', []);

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'feature_img' => $this->feature_img,
            'author_id' => $this->author_id,
            'delete_at' => $this->delete_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted,
        ];

        if (\in_array('category', $include)) {
            $data['categories'] = $this->categories;
        }

        return $data;
    }
}
