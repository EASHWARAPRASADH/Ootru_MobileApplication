<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MailSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('order_mails')->delete();

        \DB::table('order_mails')->insert(array (
            0 =>
            array (
                'id' => 1,
                'subject' => 'create',
                'mail_description' => '<p>Hi [client name],</p>
<p>Thank you for placing your order with us! Were thrilled to let you know that your order has been created and is now being processed.</p>
<p>The current status of your order is: [status]. We appreciate your support and hope you enjoy your purchase!</p>
<p>Cheers,</p>',
                'type' => 'create',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            1 =>
            array (
                'id' => 2,
                'subject' => 'active',
                'mail_description' => '<p>Dear [client name],</p>
<p>We&rsquo;re excited to inform you that your order has been activated! It is now being processed, and the current status is: [status].</p>
<p>Thank you for choosing us! We appreciate your support and look forward to delivering your items soon. If you have any questions, feel free to reach out.</p>
<p>Best regards,</p>',
                'type' => 'active',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            2 =>
            array (
                'id' => 3,
                'subject' => 'Order Arrival Notification',
                'mail_description' => '<p>Dear [client name],</p>
<p>We&rsquo;re delighted to inform you that your order [order ID] has arrived! The current status is: [status].</p>
<p>We hope you enjoy your new items! If you have any questions or need assistance, feel free to reach out to us. We truly appreciate your business.</p>
<p>Thank you for choosing us!</p>
<p>Best regards,</p>',
                'type' => 'courier_arrived',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            3 =>
            array (
                'id' => 4,
                'subject' => 'Your Order is Ready for Pickup',
                'mail_description' => '<p>Dear [client name],</p>
<p>We&rsquo;re excited to inform you that your order [order ID] is now ready for pickup! The current status is: Ready for Pickup. T</p>
<p>hank you for choosing us! If you have any questions, feel free to reach out.</p>
<p>Best regards,</p>',
                'type' => 'courier_picked_up',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            4 =>
            array (
                'id' => 5,
                'subject' => 'Your Return Request Has Been Received',
                'mail_description' => '<p>Hi [client name],</p>
<p>We have received your return request for order [order ID].</p>
<p>Thank you for your cooperation!</p>
<p>Best regards,</p>',
                'type' => 'return',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            5 =>
            array (
                'id' => 6,
                'subject' => 'Your Order Has Been Cancelled',
                'mail_description' => '<p>Hi [client name],</p>
<p>We regret to inform you that your order [order ID] has been cancelled.</p>
<p>Thank you for your understanding.</p>
<p>Best regards,</p>',
                'type' => 'cancelled',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            6 =>
            array (
                'id' => 7,
                'subject' => 'Order Status Assigned',
                'mail_description' => '<p>Hello [client name],</p>
<p>Your order [order ID] status has been assigned successfully.</p>
<p>If you need assistance, let us know!</p>
<p>Best,</p>',
                'type' => 'courier_assigned',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            7 =>
            array (
                'id' => 8,
                'subject' => 'Your Order Has Been Shipped!',
                'mail_description' => '<p>Hi [client name],</p>
<p>Good news! Your order [order ID] has been shipped..</p>
<p>Thank you for your order!</p>
<p>Best regards,</p>',
                'type' => 'shipped',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            8 =>
            array (
                'id' => 9,
                'subject' => 'Your Order Has Been Delivered!',
                'mail_description' => '<p>Hi [client name],</p>
<p>Good news! Your order [order ID] has been successfully delivered.</p>
<p>We hope you enjoy your purchase! Thank you for choosing us!</p>
<p>Best regards,</p>',
                'type' => 'completed',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            9 =>
            array (
                'id' => 10,
                'subject' => 'Your Order [order ID] Has Departed',
                'mail_description' => '<p>Hello [client name],</p>
<p>Your order [order ID] has departed and is now on its way to you.</p>
<p>We appreciate your business! Best,</p>',
                'type' => 'courier_departed',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            10=>
            array (
                'id' => 11,
                'subject' => 'Your Order Has Been Rescheduled',
                'mail_description' => '<p>Hi [client name],</p>
<p>We wanted to inform you that the delivery of your order [order ID] has been rescheduled. We will notify you once the new delivery date is confirmed.</p>
<p>Thank you for your understanding.</p>
<p>Best regards,</p>',
                'type' => 'reschedule',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            )
        ));
    }
}
