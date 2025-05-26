<?php

namespace Liamtseva\Cinema\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Liamtseva\Cinema\Enums\NotificationType;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Models\NotificationHistory;
use Liamtseva\Cinema\Models\User;

class NewEpisodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Episode $episode
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'custom-database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $anime = $this->episode->anime;
        
        return (new MailMessage)
            ->subject("Нова серія аніме: {$anime->name}")
            ->line("Вийшла нова серія аніме {$anime->name}!")
            ->line("Епізод #{$this->episode->number}: {$this->episode->title}")
            ->action('Дивитися зараз', url("/anime/{$anime->slug}/episode/{$this->episode->id}"))
            ->line('Дякуємо, що користуєтесь нашим сервісом!');
    }

    public function toCustomDatabase(User $notifiable): array
    {
        $anime = $this->episode->anime;
        
        // Create a record in NotificationHistory
        NotificationHistory::create([
            'user_id' => $notifiable->id,
            'notifiable_type' => Episode::class,
            'notifiable_id' => $this->episode->id,
            'type' => NotificationType::NEW_EPISODE->value,
            'data' => json_encode([
                'anime_id' => $anime->id,
                'anime_name' => $anime->name,
                'episode_id' => $this->episode->id,
                'episode_number' => $this->episode->number,
                'episode_title' => $this->episode->title,
            ]),
            'read_at' => null,
        ]);
        
        return [
            'type' => NotificationType::NEW_EPISODE->value,
            'anime_id' => $anime->id,
            'anime_name' => $anime->name,
            'episode_id' => $this->episode->id,
            'episode_number' => $this->episode->number,
            'episode_title' => $this->episode->title,
        ];
    }
}