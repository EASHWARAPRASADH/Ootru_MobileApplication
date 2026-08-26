<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMSTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_s_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->longText('sms_description')->nullable();
            $table->unsignedBigInteger('sms_id')->nullable();
            $table->string('type')->nullable();
            $table->string('order_status')->nullable();
            $table->timestamps();
            $table->foreign('sms_id')->references('id')->on('s_m_s_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('s_m_s_templates',function(Blueprint $table){
            $table->dropForeign(['sms_id']);
        });
        Schema::dropIfExists('s_m_s_templates');
    }
}
