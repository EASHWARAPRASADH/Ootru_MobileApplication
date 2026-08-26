<?php

use App\Models\DeliveryManDocument;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->nullable()->default('UTC')->after('remember_token');
            $table->dateTime('last_actived_at')->nullable()->after('profile_photo_path');
            $table->timestamp('document_verified_at')->nullable();
            $table->boolean('is_autoverified_document')->default(0);
            $table->boolean('is_autoverified_email')->default(0);
            $table->boolean('is_autoverified_mobile')->default(0);
        });

        $verifiedDeliveryManIds = DeliveryManDocument::where('is_verified', 1)
            ->whereNull('deleted_at')
            ->pluck('delivery_man_id');

        User::whereIn('id', $verifiedDeliveryManIds)
            ->where('user_type', 'delivery_man')
            ->whereNull('deleted_at')
            ->update(['document_verified_at' => now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
