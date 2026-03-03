<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Zresetuj swoje hasło')
                    ->greeting('Cześć, ')
                    ->line('Zapomniałeś/aś hasła? Kliknij w przycisk poniżej i zresetuj swoje hasło :) ')
                    ->action('Zresetuj hasło', $this->url)
                    ->line('Jest nam niezmiernie miło, że korzystasz z naszej aplikacji! Mamy nadzieję, że dzięki niej każdego dnia utrwalasz wiedzę zdobytą podczas kursu. To… do gry i nauki! :) ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
