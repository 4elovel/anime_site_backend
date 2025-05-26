<?php

namespace Liamtseva\Cinema\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data;
        $redirectUrl = null;
        $message = 'New notification';
        
        // Format based on notification type
        if ($this->type === 'new_episode') {
            $message = "New episode #{$data['episode_number']} of {$data['anime_name']}";
            $redirectUrl = "/anime/{$data['anime_id']}/episode/{$data['episode_id']}";
        }
        
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $message,
            'redirect_url' => $redirectUrl,
            'data' => $data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}