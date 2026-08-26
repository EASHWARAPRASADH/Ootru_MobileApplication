<?php

namespace App\Mail;

use App\Models\AppSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $subject;
    public $content;


    public function __construct($content, $subject)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        $themeColor = AppSetting::all()->pluck('color')[0];
        $logo = SettingData('app_logo','mail_template_logo');
        $email = $this->view('emails.email_otp_verification')
            ->with([
                'mailDescription' => $this->content['mailDescription'],
                'otp' => $this->content['otp'],
                'subject' => $this->subject,
                'themeColor' => $themeColor,
                'logoUrl' => $logo,
                'companyName' => config('app.name'),
                'heading' => 'Almost There!',
            ]);

        return $email;
    }
}
