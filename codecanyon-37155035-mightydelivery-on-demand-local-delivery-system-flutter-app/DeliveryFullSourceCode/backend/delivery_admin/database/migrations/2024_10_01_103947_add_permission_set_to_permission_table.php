<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPermissionSetToPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('permission', function (Blueprint $table) {
            //
        // });
        DB::table('permissions')->insert(array(
            87 =>
            array (
                'id' => 88,
                'name' => 'customersupport',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            88 =>
            array (
                'id' => 89,
                'name' => 'customersupport-show',
                'guard_name' => 'web',
                'parent_id' => 88,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            89 =>
            array (
                'id' => 90,
                'name' => 'customersupport-delete',
                'guard_name' => 'web',
                'parent_id' => 88,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            90 =>
            array (
                'id' => 91,
                'name' => 'subadmin',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            91 =>
            array (
                'id' => 92,
                'name' => 'subadmin-list',
                'guard_name' => 'web',
                'parent_id' => 91,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            92 =>
            array (
                'id' => 93,
                'name' => 'subadmin-add',
                'guard_name' => 'web',
                'parent_id' => 91,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            93 =>
            array (
                'id' => 94,
                'name' => 'subadmin-edit',
                'guard_name' => 'web',
                'parent_id' => 91,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            94 =>
            array (
                'id' => 95,
                'name' => 'subadmin-delete',
                'guard_name' => 'web',
                'parent_id' => 91,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            95 =>
            array (
                'id' => 96,
                'name' => 'couriercompanies',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            96 =>
            array (
                'id' => 97,
                'name' => 'couriercompanies-list',
                'guard_name' => 'web',
                'parent_id' => 96,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            97 =>
            array (
                'id' => 98,
                'name' => 'couriercompanies-add',
                'guard_name' => 'web',
                'parent_id' => 96,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            98 =>
            array (
                'id' => 99,
                'name' => 'couriercompanies-edit',
                'guard_name' => 'web',
                'parent_id' => 96,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            99 =>
            array (
                'id' => 100,
                'name' => 'couriercompanies-delete',
                'guard_name' => 'web',
                'parent_id' => 96,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            100 =>
            array(
                'id' => 101,
                'name' => 'claims',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            101 =>
            array (
                'id' => 102,
                'name' => 'claims-list',
                'guard_name' => 'web',
                'parent_id' => 101,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            102 =>
            array (
                'id' => 103,
                'name' => 'claims-show',
                'guard_name' => 'web',
                'parent_id' => 101,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
        ));

        DB::table('role_has_permissions')->insert(array(
            87 =>
            array(
                'permission_id' => 88,
                'role_id' => 1,
            ),
            88 =>
            array(
                'permission_id' => 89,
                'role_id' => 1,
            ),
            89 =>
            array(
                'permission_id' => 90,
                'role_id' => 1,
            ),
            90 =>
            array(
                'permission_id' => 91,
                'role_id' => 1,
            ),
            91 =>
            array(
                'permission_id' => 92,
                'role_id' => 1,
            ),
            92 =>
            array(
                'permission_id' => 93,
                'role_id' => 1,
            ),
            93 =>
            array(
                'permission_id' => 94,
                'role_id' => 1,
            ),
            94 =>
            array(
                'permission_id' => 95,
                'role_id' => 1,
            ),
            95 =>
            array(
                'permission_id' => 96,
                'role_id' => 1,
            ),
            96 =>
            array(
                'permission_id' => 97,
                'role_id' => 1,
            ),
            97 =>
            array(
                'permission_id' => 98,
                'role_id' => 1,
            ),
            98 =>
            array(
                'permission_id' => 99,
                'role_id' => 1,
            ),
            99 =>
            array(
                'permission_id' => 100,
                'role_id' => 1,
            ),
            100 =>
            array(
                'permission_id' => 101,
                'role_id' => 1,
            ),
            101 =>
            array(
                'permission_id' => 102,
                'role_id' => 1,
            ),
            102 =>
            array(
                'permission_id' => 103,
                'role_id' => 1,
            ),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission', function (Blueprint $table) {
            //
        });
    }
}
