<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use AnimeSite\Http\Resources\UserSubscriptionResource;
use AnimeSite\Http\Resources\PaymentResource;

class TariffResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'currency' => $this->currency,
            'duration_days' => $this->duration_days,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'subscriptions' => UserSubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'image' => $this->meta_image,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
