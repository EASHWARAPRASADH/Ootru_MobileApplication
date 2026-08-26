<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AddTypeToOrderMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_mails', function (Blueprint $table) {
            $table->string('type')->nullable()->after('mail_description');
        });
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\MailSeeders',
            '--force' => true 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_mails', function (Blueprint $table) {
            //
        });
    }
}
