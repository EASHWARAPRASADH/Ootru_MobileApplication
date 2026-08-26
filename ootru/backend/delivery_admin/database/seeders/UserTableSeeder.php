<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'username' => 'admin',
                'name' => 'Admin',
                'contact_number' => '9876543210',
                'address' => NULL,
                'email' => 'admin@admin.com',
                'password' => bcrypt('12345678'),
                'email_verified_at' => Carbon::now(),
                'user_type' => 'admin',
                'player_id' => NULL,
                'remember_token' => NULL,
                'last_notification_seen' => NULL,
                'status' => 1,
                'current_team_id' => NULL,
                'profile_photo_path' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'username' => 'client',
                'name' => 'Test Client',
                'contact_number' => '9876543211',
                'address' => 'Sample Street, City',
                'email' => 'client@client.com',
                'password' => bcrypt('12345678'),
                'email_verified_at' => Carbon::now(),
                'user_type' => 'client',
                'player_id' => NULL,
                'remember_token' => NULL,
                'last_notification_seen' => NULL,
                'status' => 1,
                'current_team_id' => NULL,
                'profile_photo_path' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'username' => 'deliveryman',
                'name' => 'Test Delivery Person',
                'contact_number' => '9876543212',
                'address' => 'Sample Street, City',
                'email' => 'delivery@delivery.com',
                'password' => bcrypt('12345678'),
                'email_verified_at' => Carbon::now(),
                'user_type' => 'delivery_man',
                'player_id' => NULL,
                'remember_token' => NULL,
                'last_notification_seen' => NULL,
                'status' => 1,
                'current_team_id' => NULL,
                'profile_photo_path' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));


    }
}
