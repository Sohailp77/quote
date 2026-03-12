<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $itemName,
        public int $currentStock
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Low Stock Alert: {$this->itemName}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("This is an automated alert to let you know that stock is running low.")
                    ->line("**Item:** {$this->itemName}")
                    ->line("**Current Stock:** {$this->currentStock}")
                    ->line("Please restock as soon as possible to avoid order fulfillment issues.")
                    ->action('View Inventory', url('/admin/products/inventory'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_name' => $this->itemName,
            'current_stock' => $this->currentStock,
        ];
    }
}
