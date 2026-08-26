<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendmail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $order_id;
    public $status;
    public $company_name;
    public $themeColor;
    public $client_name;

    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $content
     * @param array $data
     */
    public function __construct($subject, $content, $data)
    {
        $this->subject($subject);
        $this->content = $content;
        $this->order_id = $data['order_id'];
        $this->status = $data['status'];
        $this->company_name = $data['company_name'];
        $this->client_name = $data['client_name'];
        $this->themeColor = AppSetting::first()->color ?? '#9B6BFF';

        if ($this->status === 'completed') {
            $this->generatePDF();
        }
    }

    protected function generatePDF()
    {
        $order = Order::find($this->order_id);
        $today = Carbon::now()->format('d/m/Y');


        $companyName = Setting::where('type', 'order_invoice')->where('key', 'company_name')->first();
        $companynumber = Setting::where('type', 'order_invoice')->where('key', 'company_contact_number')->first();
        $companyAddress = Setting::where('type', 'order_invoice')->where('key', 'company_address')->first();
        $invoice = Setting::where('type', 'order_invoice')->where('key', 'company_logo')->first();


        $this->pdf = PDF::loadView('order.invoice', compact('invoice', 'companyName', 'companyAddress', 'companynumber', 'order', 'today'));
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.sendmail')
                      ->with([
                          'order_id' => $this->order_id,
                          'status' => $this->status,
                          'company_name' => $this->company_name,
                          'content' => $this->content,
                          'themeColor' => $this->themeColor,
                      ]);

        if ($this->status === 'completed' && isset($this->pdf)) {
            $email->attachData($this->pdf->output(), 'invoice_' . $this->order_id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
