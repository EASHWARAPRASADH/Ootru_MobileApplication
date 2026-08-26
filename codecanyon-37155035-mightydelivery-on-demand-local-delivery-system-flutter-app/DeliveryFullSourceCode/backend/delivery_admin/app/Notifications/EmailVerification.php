<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\VerificationCode;
use Illuminate\Support\HtmlString;

class EmailVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $code = substr(str_shuffle('0123456789'), 0, 5);  
        VerificationCode::where('user_id', $notifiable->id)->delete();
        VerificationCode::create([
            'code' => $code,
            'user_id' => $notifiable->id,
            'datetime' => date('Y-m-d H:i:s')
        ]);
        return (new MailMessage)
                    ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                    ->subject(__('message.email_verification'))
                    ->greeting(__('message.hello_name', ['name' => $notifiable->name ]) )
                    ->line(__('message.hello_thank_you_for_signing'))
                    ->line(__('message.code_to_active_your_account'))
                    ->line(new HtmlString('<b>'.$code.'<br></b>'))
                    ->line(__('message.code_is_valid_for_minutes',['minutes' => 10]))
                    ->salutation(new HtmlString(__('message.regards').'<br>'.env('APP_NAME')));
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
