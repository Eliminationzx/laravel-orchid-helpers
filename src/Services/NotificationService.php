<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Send notification to notifiable
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  array  $channels
     * @return void
     */
    public function send($notifiable, Notification $notification, array $channels = []): void
    {
        if (empty($channels)) {
            NotificationFacade::send($notifiable, $notification);
        } else {
            NotificationFacade::sendNow($notifiable, $notification, $channels);
        }
    }

    /**
     * Send notification to multiple notifiables
     *
     * @param  array  $notifiables
     * @param  Notification  $notification
     * @param  array  $channels
     * @return void
     */
    public function sendMultiple(array $notifiables, Notification $notification, array $channels = []): void
    {
        foreach ($notifiables as $notifiable) {
            $this->send($notifiable, $notification, $channels);
        }
    }

    /**
     * Send notification via database channel
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function sendToDatabase($notifiable, Notification $notification): void
    {
        NotificationFacade::send($notifiable, $notification, ['database']);
    }

    /**
     * Send notification via mail channel
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function sendToMail($notifiable, Notification $notification): void
    {
        NotificationFacade::send($notifiable, $notification, ['mail']);
    }

    /**
     * Send notification via broadcast channel
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function sendToBroadcast($notifiable, Notification $notification): void
    {
        NotificationFacade::send($notifiable, $notification, ['broadcast']);
    }

    /**
     * Send notification via SMS channel (if available)
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function sendToSms($notifiable, Notification $notification): void
    {
        $channels = ['sms'];
        
        // Check if SMS channel is available
        if ($this->isSmsChannelAvailable()) {
            NotificationFacade::send($notifiable, $notification, $channels);
        }
    }

    /**
     * Send notification via Slack channel (if available)
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function sendToSlack($notifiable, Notification $notification): void
    {
        $channels = ['slack'];
        
        // Check if Slack channel is available
        if ($this->isSlackChannelAvailable()) {
            NotificationFacade::send($notifiable, $notification, $channels);
        }
    }

    /**
     * Mark notification as read
     *
     * @param  mixed  $notification
     * @return bool
     */
    public function markAsRead($notification): bool
    {
        if (method_exists($notification, 'markAsRead')) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for notifiable
     *
     * @param  mixed  $notifiable
     * @return int
     */
    public function markAllAsRead($notifiable): int
    {
        if (method_exists($notifiable, 'unreadNotifications')) {
            return $notifiable->unreadNotifications()->update(['read_at' => now()]);
        }
        
        return 0;
    }

    /**
     * Get unread notifications count for notifiable
     *
     * @param  mixed  $notifiable
     * @return int
     */
    public function getUnreadCount($notifiable): int
    {
        if (method_exists($notifiable, 'unreadNotifications')) {
            return $notifiable->unreadNotifications()->count();
        }
        
        return 0;
    }

    /**
     * Get notifications for notifiable
     *
     * @param  mixed  $notifiable
     * @param  int  $limit
     * @param  bool  $unreadOnly
     * @return mixed
     */
    public function getNotifications($notifiable, int $limit = 20, bool $unreadOnly = false)
    {
        if (!method_exists($notifiable, 'notifications')) {
            return collect();
        }
        
        $query = $notifiable->notifications();
        
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }
        
        return $query->latest()->limit($limit)->get();
    }

    /**
     * Clear all notifications for notifiable
     *
     * @param  mixed  $notifiable
     * @return int
     */
    public function clearAll($notifiable): int
    {
        if (method_exists($notifiable, 'notifications')) {
            return $notifiable->notifications()->delete();
        }
        
        return 0;
    }

    /**
     * Clear old notifications for notifiable
     *
     * @param  mixed  $notifiable
     * @param  int  $days
     * @return int
     */
    public function clearOld($notifiable, int $days = 30): int
    {
        if (method_exists($notifiable, 'notifications')) {
            $cutoffDate = now()->subDays($days);
            return $notifiable->notifications()->where('created_at', '<', $cutoffDate)->delete();
        }
        
        return 0;
    }

    /**
     * Create database notification
     *
     * @param  array  $data
     * @param  string  $type
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function createDatabaseNotification(array $data, string $type, $notifiable)
    {
        if (!method_exists($notifiable, 'notifications')) {
            return null;
        }
        
        return $notifiable->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => $type,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    /**
     * Get available notification channels
     *
     * @return array
     */
    public function getAvailableChannels(): array
    {
        $channels = ['database', 'mail', 'broadcast'];
        
        if ($this->isSmsChannelAvailable()) {
            $channels[] = 'sms';
        }
        
        if ($this->isSlackChannelAvailable()) {
            $channels[] = 'slack';
        }
        
        return $channels;
    }

    /**
     * Check if SMS channel is available
     *
     * @return bool
     */
    public function isSmsChannelAvailable(): bool
    {
        return class_exists('Illuminate\Notifications\Channels\SmsChannel') ||
               config('services.twilio.sid') !== null;
    }

    /**
     * Check if Slack channel is available
     *
     * @return bool
     */
    public function isSlackChannelAvailable(): bool
    {
        return class_exists('Illuminate\Notifications\Channels\SlackWebhookChannel') ||
               config('services.slack.webhook_url') !== null;
    }

    /**
     * Send immediate notification (bypass queue)
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  array  $channels
     * @return void
     */
    public function sendNow($notifiable, Notification $notification, array $channels = []): void
    {
        NotificationFacade::sendNow($notifiable, $notification, $channels);
    }

    /**
     * Queue notification for later delivery
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  array  $channels
     * @param  string|null  $queue
     * @return void
     */
    public function queue($notifiable, Notification $notification, array $channels = [], ?string $queue = null): void
    {
        if ($queue) {
            $notification->onQueue($queue);
        }
        
        NotificationFacade::send($notifiable, $notification, $channels);
    }

    /**
     * Send notification with delay
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  array  $channels
     * @return void
     */
    public function sendWithDelay($notifiable, Notification $notification, $delay, array $channels = []): void
    {
        $notification->delay($delay);
        NotificationFacade::send($notifiable, $notification, $channels);
    }

    /**
     * Format notification message
     *
     * @param  string  $message
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    public function formatMessage(string $message, array $replace = [], ?string $locale = null): string
    {
        return trans($message, $replace, $locale);
    }

    /**
     * Create notification from array data
     *
     * @param  array  $data
     * @param  string  $type
     * @return Notification
     */
    public function createNotificationFromArray(array $data, string $type): Notification
    {
        return new class($data, $type) extends Notification {
            private $data;
            private $type;
            
            public function __construct(array $data, string $type)
            {
                $this->data = $data;
                $this->type = $type;
            }
            
            public function via($notifiable): array
            {
                return ['database'];
            }
            
            public function toArray($notifiable): array
            {
                return array_merge($this->data, [
                    'type' => $this->type,
                    'timestamp' => now()->toISOString(),
                ]);
            }
            
            public function toDatabase($notifiable): array
            {
                return $this->toArray($notifiable);
            }
        };
    }
}