<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StorefrontPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'phone' => $notifiable->phone,
        ]);

        return (new MailMessage)
            ->subject('بازیابی رمز عبور '.site_name())
            ->line('درخواست بازیابی رمز عبور برای حساب شما ثبت شد.')
            ->action('تنظیم رمز جدید', $url)
            ->line('اگر این درخواست از طرف شما نبود، این ایمیل را نادیده بگیرید.');
    }
}
