<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            //fn means the function that runs emditly
            'category' => $this->whenLoaded('category', fn () => $this->category->name),
            'assigned_users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
