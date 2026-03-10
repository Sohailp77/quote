<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteCreatedNotification extends Notification
{
    use Queueable;

    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your New Quote - {$this->quote->reference_id}")
            ->greeting("Hello {$this->quote->customer_name},")
            ->line("We have generated a new quote for you.")
            ->line("Reference ID: {$this->quote->reference_id}")
            ->line("Total Amount: {$this->quote->total_amount}")
            ->action('View Quote', url("/quotes/{$this->quote->id}/public"))
            ->line('Thank you for choosing us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
