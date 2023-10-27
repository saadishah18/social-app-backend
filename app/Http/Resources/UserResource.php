<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'username' => $this->user_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => $this->role,
            'plan_id' => $this->plan_id,
            'is_approved' => $this->is_approved,
            'is_active' => $this->is_active,
            'plan' => $this->plan->name,
            'created_by_email' => $this->created_by != null ? $this->createdBy->email : '',
            'created_by_name' => $this->created_by != null ? $this->createdBy->user_name : '',
            'logo' => $this->logo != null ? asset('storage/images/logo/'.$this->logo) : '',
            'profile_image' => $this->profile_image != null ? asset('storage/'.$this->profile_image) : ''
        ];
    }
}
