<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public $userType;

    /**
     * Create a new notification instance.
     */
    public function __construct($userType = 'user')
    {
        $this->userType = $userType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verificação de E-mail - GoPubli')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Obrigado por se cadastrar no GoPubli!')
            ->line('Por favor, clique no botão abaixo para verificar seu endereço de e-mail.')
            ->action('Verificar E-mail', $verificationUrl)
            ->line('Se você não criou uma conta, nenhuma ação adicional é necessária.')
            ->salutation('Atenciosamente, Equipe GoPubli');
    }

    /**
     * Get the verification URL for the notification.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'type' => $this->userType,
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
