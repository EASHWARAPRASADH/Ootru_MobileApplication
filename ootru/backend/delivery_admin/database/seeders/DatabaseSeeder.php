<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            RoleTableSeeder::class, 
            UserTableSeeder::class,
            ModelHasRolesTableSeeder::class,
            PermissionTableSeeder::class,
            RoleHasPermissionsTableSeeder::class,
            ModelHasPermissionsTableSeeder::class,
            AppSettingTableSeeder::class,
            MailSeeders::class,
            SmsSeeders::class,
            ScreenkeywordSeeder::class,
            LanguageDefaultListSeeder::class
        ]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
