<?php

namespace AnimeSite\DTOs\Auth;

use AnimeSite\Models\User;

class AuthResponseDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $token,
        public readonly string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'token' => $this->token,
            'message' => $this->message,
        ];
    }
}