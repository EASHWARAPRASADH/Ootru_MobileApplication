<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SmsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure foreign key record exists
        \DB::table('s_m_s_settings')->updateOrInsert(
            ['id' => 1],
            [
                'title' => 'Twilio', 
                'type' => 'twilio', 
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('s_m_s_templates')->delete();
        // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('s_m_s_templates')->insert(array (
            0 =>
            array (
                'id' => 1,
                'subject' => 'order confirmation',
                'sms_description' => 
                '<p>Thank you for your order, [Customer Name]! Your order #[Order ID] has been confirmed. Well update you once its out for delivery. Track here: [Tracking Link].....</p>',
                'sms_id' => 1,
                'order_status' => 'create',
                'type' => 'order_confirmation',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            1 =>
            array (
                'id' => 2,
                'subject' => 'you have parcel',
                'sms_description' => 
                '<p>Hello [Customer Name], you have received a parcel from [Sender Name]. Your order number is #[Order ID]. You can track it here: [Tracking Link]</p>',
                'sms_id' => 1,
                'order_status' => 'create_receiver',
                'type' => 'you_have_parcel',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            2 =>
            array (
                'id' => 3,
                'subject' => 'out_for_delivery',
                'sms_description' => 
                '<p>Hello [Customer Name], your order #[Order ID] is out for delivery and will reach you soon. Keep your phone handy! Track here: [Tracking Link]</p>',
                'sms_id' => 1,
                'order_status' => 'courier_arrived',
                'type' => 'out_for_delivery',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            3 =>
            array (
                'id' => 4,
                'subject' => 'delivered_successfully',
                'sms_description' => 
                '<p>Hi [Customer Name], your order #[Order ID] has been delivered. We hope you love it.</p>',
                'sms_id' => 1,
                'order_status' => 'completed',
                'type' => 'delivered_successfully',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            4 =>
            array (
                'id' => 5,
                'subject' => 'delivery_attempt_failed',
                'sms_description' => 
                '<p>Hi [Customer Name], we tried to deliver your order #[Order ID] but were unable to reach you.Your order #[Order ID]is reschedule at [Date Time].</p>',
                'sms_id' => 1,
                'order_status' => 'reschedule',
                'type' => 'delivery_attempt_failed',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            5 =>
            array (
                'id' => 6,
                'subject' => 'new_delivery_assignment',
                'sms_description' => 
                '<p>Hi [Delievry_Man Name], a new order #[Order ID] has been assigned to you. Please confirm it at your earliest convenience.</p>',
                'sms_id' => 1,
                'order_status' => 'courier_assigned',
                'type' => 'new_delivery_assignment',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            6 =>
            array (
                'id' => 7,
                'subject' => 'pickup_verification_code',
                'sms_description' => 
                '<p>Hi [Customer Name], use OTP is [OTP Code] to pick up order #[Order ID] from Pickup Location. Do not share this OTP with anyone.',
                'sms_id' => 1,
                'order_status' => 'courier_picked_up',
                'type' => 'pickup_verification_code',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
            7 =>
            array (
                'id' => 8,
                'subject' => 'delivery_verification_code',
                'sms_description' => 
                '<p>Hi [Customer Name], use OTP is [OTP Code] to receive your parcel #[Order ID]. Please provide this code to the delivery agent.</p>',
                'sms_id' => 1,
                'order_status' => 'courier_picked_up_delivery_code',
                'type' => 'delivery_verification_code',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ),
        ));
    }
}
