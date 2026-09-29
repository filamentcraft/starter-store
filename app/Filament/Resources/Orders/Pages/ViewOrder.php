<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Pages;

use App\Actions\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return "Order {$this->getRecord()->getAttribute('number')}";
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->advance(OrderStatus::Confirmed, 'Confirm', from: OrderStatus::Pending),
            $this->advance(OrderStatus::Shipped, 'Mark shipped', from: OrderStatus::Confirmed),
            $this->advance(OrderStatus::Delivered, 'Mark delivered', from: OrderStatus::Shipped),
            Action::make('cancel')
                ->label('Cancel order')
                ->color('danger')
                ->icon(OrderStatus::Cancelled->getIcon())
                ->requiresConfirmation()
                ->modalDescription('The items go back into stock. This cannot be undone.')
                ->visible(fn (Order $record): bool => in_array($record->status, [OrderStatus::Pending, OrderStatus::Confirmed], true))
                ->action(fn (Order $record) => $this->change($record, OrderStatus::Cancelled)),
        ];
    }

    private function advance(OrderStatus $to, string $label, OrderStatus $from): Action
    {
        return Action::make($to->value)
            ->label($label)
            ->icon($to->getIcon())
            ->visible(fn (Order $record): bool => $record->status === $from)
            ->action(fn (Order $record) => $this->change($record, $to));
    }

    private function change(Order $order, OrderStatus $status): void
    {
        app(ChangeOrderStatus::class)->handle($order, $status);

        Notification::make()->success()->title("Order {$status->getLabel()}")->send();

        $this->refreshFormData(['status']);
    }
}
