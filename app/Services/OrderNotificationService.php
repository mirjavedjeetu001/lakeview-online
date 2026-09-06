<?php

namespace App\Services;

use App\Models\CustomCakeOrder;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    public function sendOrderCreated(Order $order): void
    {
        $settings = Setting::getMailerSettings();
        $this->configureMailer($settings);

        $order->loadMissing(['items', 'branch', 'deliveryArea']);
        $subject = 'New order ' . $order->order_number . ' - Lake View';
        $body = $this->orderBody($order);

        if ($settings['order_notifications'] && $settings['recipients']) {
            $this->send($settings['recipients'], $subject, $body, $settings);
        }

        if ($settings['customer_notifications'] && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            $this->send([$order->customer_email], 'Order confirmation ' . $order->order_number . ' - Lake View', $body, $settings);
        }
    }

    public function sendCustomCakeCreated(CustomCakeOrder $order): void
    {
        $settings = Setting::getMailerSettings();
        $this->configureMailer($settings);

        $order->loadMissing(['branch', 'deliveryArea']);
        $subject = 'New custom cake request ' . $order->order_number . ' - Lake View';
        $body = "New custom cake request\n\nOrder: {$order->order_number}\nCustomer: {$order->customer_name}\nPhone: {$order->customer_phone}\nEmail: " . ($order->customer_email ?: 'Not provided') . "\nBranch: " . ($order->branch?->name ?: 'N/A') . "\nPickup date: {$order->delivery_date}\nCake type: " . ($order->cake_type ?: 'Not specified') . "\nCake size: " . ($order->cake_size ?: 'Not specified') . "\nFlavor: " . ($order->cake_flavor ?: 'Not specified') . "\nMessage: " . ($order->message_on_cake ?: 'None') . "\nNotes: " . ($order->notes ?: 'None');

        if ($settings['order_notifications'] && $settings['recipients']) {
            $this->send($settings['recipients'], $subject, $body, $settings);
        }

        if ($settings['customer_notifications'] && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            $this->send([$order->customer_email], 'Custom cake request received ' . $order->order_number . ' - Lake View', $body, $settings);
        }
    }

    private function orderBody(Order $order): string
    {
        $items = $order->items
            ->map(fn ($item) => "- {$item->product_name} x {$item->quantity}: ৳" . number_format((float) $item->total, 2))
            ->implode("\n");

        return "New order received\n\nOrder: {$order->order_number}\nCustomer: {$order->customer_name}\nPhone: {$order->customer_phone}\nEmail: " . ($order->customer_email ?: 'Not provided') . "\nDelivery: " . ($order->delivery_type === 'pickup' ? 'Pickup' : 'Home Delivery') . "\nBranch: " . ($order->branch?->name ?: 'N/A') . "\nArea: " . ($order->deliveryArea?->name ?: 'N/A') . "\nAddress: " . ($order->customer_address ?: 'N/A') . "\n\nItems:\n{$items}\n\nSubtotal: ৳" . number_format((float) $order->subtotal, 2) . "\nDelivery: ৳" . number_format((float) $order->delivery_charge, 2) . "\nDiscount: ৳" . number_format((float) $order->discount, 2) . "\nTotal: ৳" . number_format((float) $order->total, 2) . "\n\nNotes: " . ($order->notes ?: 'None');
    }

    private function send(array $recipients, string $subject, string $body, array $settings): void
    {
        try {
            Mail::raw($body, function ($message) use ($recipients, $subject, $settings) {
                $message->to($recipients)->subject($subject);
                if ($settings['reply_to']) {
                    $message->replyTo($settings['reply_to']);
                }
            });
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function configureMailer(array $settings): void
    {
        config([
            'mail.default' => $settings['mailer'],
            'mail.from.address' => $settings['from_address'],
            'mail.from.name' => $settings['from_name'],
            'mail.mailers.smtp.host' => $settings['host'],
            'mail.mailers.smtp.port' => $settings['port'],
            'mail.mailers.smtp.scheme' => $settings['scheme'],
            'mail.mailers.smtp.username' => $settings['username'],
            'mail.mailers.smtp.password' => $settings['password'],
            'mail.mailers.smtp.timeout' => 10,
        ]);
    }
}
