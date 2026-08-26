<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('permissions')->delete();

        DB::table('permissions')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'role',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'role-add',
                'guard_name' => 'web',
                'parent_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'role-list',
                'guard_name' => 'web',
                'parent_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'permission',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'permission-add',
                'guard_name' => 'web',
                'parent_id' => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'permission-list',
                'guard_name' => 'web',
                'parent_id' => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'users',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'users-list',
                'guard_name' => 'web',
                'parent_id' => 7,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'users-add',
                'guard_name' => 'web',
                'parent_id' => 7,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'users-edit',
                'guard_name' => 'web',
                'parent_id' => 7,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'users-show',
                'guard_name' => 'web',
                'parent_id' => 7,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'users-delete',
                'guard_name' => 'web',
                'parent_id' => 7,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'deliveryman',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'deliveryman-add',
                'guard_name' => 'web',
                'parent_id' => 13,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'deliveryman-edit',
                'guard_name' => 'web',
                'parent_id' => 13,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'deliveryman-list',
                'guard_name' => 'web',
                'parent_id' => 13,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'deliveryman-show',
                'guard_name' => 'web',
                'parent_id' => 13,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'deliveryman-delete',
                'guard_name' => 'web',
                'parent_id' => 13,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'document',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'document-add',
                'guard_name' => 'web',
                'parent_id' => 19,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'document-edit',
                'guard_name' => 'web',
                'parent_id' => 19,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'document-list',
                'guard_name' => 'web',
                'parent_id' => 19,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'document-delete',
                'guard_name' => 'web',
                'parent_id' => 19,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'city',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'city-add',
                'guard_name' => 'web',
                'parent_id' => 24,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'city-edit',
                'guard_name' => 'web',
                'parent_id' => 24,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'city-list',
                'guard_name' => 'web',
                'parent_id' => 24,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'city-show',
                'guard_name' => 'web',
                'parent_id' => 24,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'city-delete',
                'guard_name' => 'web',
                'parent_id' => 24,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'country',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'country-add',
                'guard_name' => 'web',
                'parent_id' => 30,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            31 =>
            array(
                'id' => 32,
                'name' => 'country-edit',
                'guard_name' => 'web',
                'parent_id' => 30,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            32 =>
            array(
                'id' => 33,
                'name' => 'country-list',
                'guard_name' => 'web',
                'parent_id' => 30,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            33 =>
            array(
                'id' => 34,
                'name' => 'country-delete',
                'guard_name' => 'web',
                'parent_id' => 30,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            34 =>
            array(
                'id' => 35,
                'name' => 'vehicle',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            35 =>
            array(
                'id' => 36,
                'name' => 'vehicle-add',
                'guard_name' => 'web',
                'parent_id' => 35,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            36 =>
            array(
                'id' => 37,
                'name' => 'vehicle-edit',
                'guard_name' => 'web',
                'parent_id' => 35,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            37 =>
            array(
                'id' => 38,
                'name' => 'vehicle-list',
                'guard_name' => 'web',
                'parent_id' => 35,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            38 =>
            array(
                'id' => 39,
                'name' => 'vehicle-show',
                'guard_name' => 'web',
                'parent_id' => 35,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            39 =>
            array(
                'id' => 40,
                'name' => 'vehicle-delete',
                'guard_name' => 'web',
                'parent_id' => 35,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            40 =>
            array(
                'id' => 41,
                'name' => 'extacharge',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            41 =>
            array(
                'id' => 42,
                'name' => 'extracharge-add',
                'guard_name' => 'web',
                'parent_id' => 41,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            42 =>
            array(
                'id' => 43,
                'name' => 'extracharge-edit',
                'guard_name' => 'web',
                'parent_id' => 41,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            43 =>
            array(
                'id' => 44,
                'name' => 'extracharge-list',
                'guard_name' => 'web',
                'parent_id' => 41,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            44 =>
            array(
                'id' => 45,
                'name' => 'extracharge-delete',
                'guard_name' => 'web',
                'parent_id' => 41,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            45 =>
            array(
                'id' => 46,
                'name' => 'deliverymandocument',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            46 =>
            array(
                'id' => 47,
                'name' => 'deliverymandocument-add',
                'guard_name' => 'web',
                'parent_id' => 46,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            47 =>
            array(
                'id' => 48,
                'name' => 'staticdata',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            48 =>
            array(
                'id' => 49,
                'name' => 'staticdata-add',
                'guard_name' => 'web',
                'parent_id' => 48,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            49 =>
            array(
                'id' => 50,
                'name' => 'staticdata-edit',
                'guard_name' => 'web',
                'parent_id' => 48,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            50 =>
            array(
                'id' => 51,
                'name' => 'staticdata-delete',
                'guard_name' => 'web',
                'parent_id' => 48,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            51 =>
            array(
                'id' => 52,
                'name' => 'staticdata-list',
                'guard_name' => 'web',
                'parent_id' => 48,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            52 =>
            array(
                'id' => 53,
                'name' => 'order',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            53 =>
            array(
                'id' => 54,
                'name' => 'order-list',
                'guard_name' => 'web',
                'parent_id' => 53,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            54 =>
            array(
                'id' => 55,
                'name' => 'order-show',
                'guard_name' => 'web',
                'parent_id' => 53,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            55 =>
            array(
                'id' => 56,
                'name' => 'order-delete',
                'guard_name' => 'web',
                'parent_id' => 53,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            56 =>
            array(
                'id' => 57,
                'name' => 'order-add',
                'guard_name' => 'web',
                'parent_id' => 53,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            57 =>
            array(
                'id' => 58,
                'name' => 'order-edit',
                'guard_name' => 'web',
                'parent_id' => 53,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            58 =>
            array(
                'id' => 59,
                'name' => 'withdrawrequest',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            59 =>
            array(
                'id' => 60,
                'name' => 'withdrawrequest-show',
                'guard_name' => 'web',
                'parent_id' => 59,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            60 =>
            array(
                'id' => 61,
                'name' => 'screen',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            61 =>
            array(
                'id' => 62,
                'name' => 'screen-list',
                'guard_name' => 'web',
                'parent_id' => 61,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            62 =>
            array(
                'id' => 63,
                'name' => 'defaultkeyword',
                'guard_name' => 'web',
                'parent_id' => NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            63 =>
            array(
                'id' => 64,
                'name' => 'defaultkeyword-list',
                'guard_name' => 'web',
                'parent_id' => 63,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            64 =>
            array(
                'id' => 65,
                'name' => 'defaultkeyword-add',
                'guard_name' => 'web',
                'parent_id' => 63,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            65 =>
            array(
                'id' => 66,
                'name' => 'defaultkeyword-edit',
                'guard_name' => 'web',
                'parent_id' => 63,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            66 =>
            array(
                'id' => 67,
                'name' => 'languagelist',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            67 =>
            array(
                'id' => 68,
                'name' => 'languagelist-list',
                'guard_name' => 'web',
                'parent_id' => 67,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            68 =>
            array(
                'id' => 69,
                'name' => 'languagelist-add',
                'guard_name' => 'web',
                'parent_id' => 67,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            69 =>
            array(
                'id' => 70,
                'name' => 'languagelist-edit',
                'guard_name' => 'web',
                'parent_id' => 67,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            70 =>
            array(
                'id' => 71,
                'name' => 'languagelist-delete',
                'guard_name' => 'web',
                'parent_id' => 67,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            71 =>
            array(
                'id' => 72,
                'name' => 'languagewithkeyword',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            72 =>
            array(
                'id' => 73,
                'name' => 'languagewithkeyword-list',
                'guard_name' => 'web',
                'parent_id' => 72,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            73 =>
            array(
                'id' => 74,
                'name' => 'languagewithkeyword-edit',
                'guard_name' => 'web',
                'parent_id' => 72,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            74 =>
            array(
                'id' => 75,
                'name' => 'bulkimport',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            75 =>
            array(
                'id' => 76,
                'name' => 'bulkimport-list',
                'guard_name' => 'web',
                'parent_id' => 75,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            76 =>
            array(
                'id' => 77,
                'name' => 'push notification',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            77 =>
            array(
                'id' => 78,
                'name' => 'push notification-list',
                'guard_name' => 'web',
                'parent_id' => 77,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            78 =>
            array(
                'id' => 79,
                'name' => 'push notification-add',
                'guard_name' => 'web',
                'parent_id' => 77,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            79 =>
            array(
                'id' => 80,
                'name' => 'push notification-delete',
                'guard_name' => 'web',
                'parent_id' => 77,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            80 =>
            array(
                'id' => 81,
                'name' => 'withdrawrequest-list',
                'guard_name' => 'web',
                'parent_id' => 59,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            81 =>
            array(
                'id' => 82,
                'name' => 'withdrawrequest-add',
                'guard_name' => 'web',
                'parent_id' => 59,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            82 =>
            array (
                'id' => 83,
                'name' => 'pages',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            83 =>
            array (
                'id' => 84,
                'name' => 'pages-list',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            84 =>
            array (
                'id' => 85,
                'name' => 'pages-add',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            85 =>
            array (
                'id' => 86,
                'name' => 'pages-edit',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            86 =>
            array (
                'id' => 87,
                'name' => 'pages-delete',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
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
            90 =>
            array (
                'id' => 91,
                'name' => 'customersupport-delete',
                'guard_name' => 'web',
                'parent_id' => 88,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            91 =>
            array (
                'id' => 92,
                'name' => 'subadmin',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            92 =>
            array (
                'id' => 93,
                'name' => 'subadmin-list',
                'guard_name' => 'web',
                'parent_id' => 92,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            93 =>
            array (
                'id' => 94,
                'name' => 'subadmin-add',
                'guard_name' => 'web',
                'parent_id' => 92,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            94 =>
            array (
                'id' => 95,
                'name' => 'subadmin-edit',
                'guard_name' => 'web',
                'parent_id' => 92,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            95 =>
            array (
                'id' => 96,
                'name' => 'subadmin-delete',
                'guard_name' => 'web',
                'parent_id' => 92,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            97 =>
            array (
                'id' => 98,
                'name' => 'couriercompanies',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            98 =>
            array (
                'id' => 99,
                'name' => 'couriercompanies-list',
                'guard_name' => 'web',
                'parent_id' => 98,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            99 =>
            array (
                'id' => 100,
                'name' => 'couriercompanies-add',
                'guard_name' => 'web',
                'parent_id' => 98,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            100 =>
            array (
                'id' => 101,
                'name' => 'couriercompanies-edit',
                'guard_name' => 'web',
                'parent_id' => 98,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            101 =>
            array (
                'id' => 102,
                'name' => 'couriercompanies-delete',
                'guard_name' => 'web',
                'parent_id' => 98,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            102 =>
            array(
                'id' => 103,
                'name' => 'claims',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            103 =>
            array (
                'id' => 104,
                'name' => 'claims-list',
                'guard_name' => 'web',
                'parent_id' => 103,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            104 =>
            array (
                'id' => 105,
                'name' => 'claims-show',
                'guard_name' => 'web',
                'parent_id' => 103,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            105 =>
            array (
                'id' => 106,
                'name' => 'customersupport-list',
                'guard_name' => 'web',
                'parent_id' => 88,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            106 =>
            array(
                'id' => 107,
                'name' => 'emergency',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            107 =>
            array (
                'id' => 108,
                'name' => 'emergency-list',
                'guard_name' => 'web',
                'parent_id' => 107,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            108 =>
            array (
                'id' => 109,
                'name' => 'emergency-map',
                'guard_name' => 'web',
                'parent_id' => 107,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            109 =>
            array (
                'id' => 110,
                'name' => 'emergency-show',
                'guard_name' => 'web',
                'parent_id' => 107,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
        ));
    }
}
