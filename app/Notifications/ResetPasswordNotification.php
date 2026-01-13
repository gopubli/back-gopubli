<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $userType;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $userType = 'user')
    {
        $this->token = $token;
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
        $url = $this->getResetUrl();

        return (new MailMessage)
            ->subject('Redefinição de Senha - GoPubli')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
            ->action('Redefinir Senha', $url)
            ->line('Este link de redefinição de senha expirará em 60 minutos.')
            ->line('Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.')
            ->salutation('Atenciosamente, Equipe GoPubli');
    }

    /**
     * Get the reset URL for the notification.
     */
    protected function getResetUrl(): string
    {
        // Aqui você pode customizar a URL de reset para cada tipo de usuário
        // Exemplo: diferentes URLs para admin, empresa e influencer
        $baseUrl = config('app.frontend_url', config('app.url'));
        
        return sprintf(
            '%s/reset-password/%s?token=%s&type=%s',
            $baseUrl,
            $this->userType,
            $this->token,
            $this->userType
        );
    }
}
