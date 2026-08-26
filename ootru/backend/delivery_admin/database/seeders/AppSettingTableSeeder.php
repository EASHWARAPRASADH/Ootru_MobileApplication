<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('app_settings')->delete();

        DB::table('app_settings')->insert(array (
            0 =>
            array (
                'id' => 1,
                'site_name' => 'Mighty Delivery',
                'site_email' => NULL,
                'site_description' => NULL,
                'site_copyright' => NULL,
                'facebook_url' => NULL,
                'instagram_url' => NULL,
                'twitter_url' => NULL,
                'linkedin_url' => NULL,
                'language_option' => '["en"]',
                'support_number' => NULL,
                'color' => '#5e3c9e',
            ),
        ));
    }
}
