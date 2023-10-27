<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->isEmpty()) {
            return [
                'total_records' => 0,
                'page_records' => 0,
                'per_page' => (int)$request->get('per_page', config('utility.per_page')),
                'current_page' => 1,
                'total_pages' => 1
            ];
        }
//        dd($this->to);
        return [
            'total_records' => (int)$this->total(),
            'page_records' => (int)$this->count(),
            'per_page' => (int)$this->perPage(),
            'current_page' => (int)$this->currentPage(),
            'total_pages' => (int)$this->lastPage(),
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
