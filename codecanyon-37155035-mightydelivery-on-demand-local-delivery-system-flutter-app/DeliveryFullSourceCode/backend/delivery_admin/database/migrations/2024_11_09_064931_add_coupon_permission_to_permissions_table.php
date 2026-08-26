<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCouponPermissionToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            DB::table('permissions')->insert(array(
                103 =>
                array (
                    'id' => 104,
                    'name' => 'coupon',
                    'guard_name' => 'web',
                    'parent_id' => NUll,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                ),
                104 =>
                array (
                    'id' => 105,
                    'name' => 'coupon-list',
                    'guard_name' => 'web',
                    'parent_id' => 104,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                ),
                105 =>
                array (
                    'id' => 106,
                    'name' => 'coupon-add',
                    'guard_name' => 'web',
                    'parent_id' => 104,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                ),
                106 =>
                array (
                    'id' => 107,
                    'name' => 'coupon-edit',
                    'guard_name' => 'web',
                    'parent_id' => 104,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                ),
                107 =>
                array (
                    'id' => 108,
                    'name' => 'coupon-delete',
                    'guard_name' => 'web',
                    'parent_id' => 104,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => NULL,
                ),
            ));

            DB::table('role_has_permissions')->insert(array(
                103 =>
                array(
                    'permission_id' => 104,
                    'role_id' => 1,
                ),
                104 =>
                array(
                    'permission_id' => 105,
                    'role_id' => 1,
                ),
                105 =>
                array(
                    'permission_id' => 106,
                    'role_id' => 1,
                ),
                106 =>
                array(
                    'permission_id' => 107,
                    'role_id' => 1,
                ),
                107 =>
                array(
                    'permission_id' => 108,
                    'role_id' => 1,
                ),
                108 =>
                array(
                    'permission_id' => 57,
                    'role_id' => 2,
                ),
                109 =>
                array(
                    'permission_id' =>  54,
                    'role_id' => 2,
                ),
                110 =>
                array(
                    'permission_id' =>  81,
                    'role_id' => 2,
                ),
            ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
}
