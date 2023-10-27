<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->id,
            'post_by' => $this->user->user_name,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'client_signature' => $this->client_signature != null? imagePath($this->client_signature) :null,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'post_date' => Carbon::parse($this->created_at)->toFormattedDateString(),
            'status' => $this->status,
            'post_image' => imagePath($this->post_image),
            'fb_handler' => $this->fb_handler,
            'insta_handler' => $this->insta_handler,
            'tiktok_handler' => $this->tiktok_handler,
            'sanpchat_handler' => $this->sanpchat_handler,
        ];
    }
}
