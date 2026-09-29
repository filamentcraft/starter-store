<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReceived extends Notification
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("We have your order, {$this->order->number}")
            ->greeting("Thank you, {$this->order->customer_name}.")
            ->line('Your order is in. We pack everything by hand, so we will email again when it leaves the studio.');

        $this->order->items->each(fn (OrderItem $item) => $message->line(
            "{$item->quantity} × {$item->name}".($item->variant ? " ({$item->variant})" : '').' — '.Money::format($item->line_total),
        ));

        return $message
            ->line('Shipping: '.($this->order->shipping === 0 ? 'free' : Money::format($this->order->shipping)))
            ->line('**Total, paid on delivery: '.Money::format($this->order->total).'**')
            ->line("Delivering to {$this->order->address}, {$this->order->city}.")
            ->salutation((string) config('app.name'));
    }
}
