<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->tinyInteger('status')->nullable()->default('1');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            // $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], 'permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    ['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            // $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    ['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            // $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            // $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

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
            array(
                'id' => 83,
                'name' => 'pages',
                'guard_name' => 'web',
                'parent_id' => NUll,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            83 =>
            array(
                'id' => 84,
                'name' => 'pages-list',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            84 =>
            array(
                'id' => 85,
                'name' => 'pages-add',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            85 =>
            array(
                'id' => 86,
                'name' => 'pages-edit',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            86 =>
            array(
                'id' => 87,
                'name' => 'pages-delete',
                'guard_name' => 'web',
                'parent_id' => 83,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
        ));

        DB::table('roles')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'client',
                'guard_name' => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'delivery_man',
                'guard_name' => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => NULL,
            ),
        ));

        DB::table('role_has_permissions')->insert(array(
            0 =>
            array(
                'permission_id' => 1,
                'role_id' => 1,
            ),
            1 =>
            array(
                'permission_id' => 2,
                'role_id' => 1,
            ),
            2 =>
            array(
                'permission_id' => 3,
                'role_id' => 1,
            ),
            3 =>
            array(
                'permission_id' => 4,
                'role_id' => 1,
            ),
            4 =>
            array(
                'permission_id' => 5,
                'role_id' => 1,
            ),
            5 =>
            array(
                'permission_id' => 6,
                'role_id' => 1,
            ),

            6 =>
            array(
                'permission_id' => 7,
                'role_id' => 1,
            ),
            7 =>
            array(
                'permission_id' => 8,
                'role_id' => 1,
            ),
            8 =>
            array(
                'permission_id' => 9,
                'role_id' => 1,
            ),
            9 =>
            array(
                'permission_id' => 10,
                'role_id' => 1,
            ),
            10 =>
            array(
                'permission_id' => 11,
                'role_id' => 1,
            ),
            11 =>
            array(
                'permission_id' => 12,
                'role_id' => 1,
            ),
            12 =>
            array(
                'permission_id' => 13,
                'role_id' => 1,
            ),
            13 =>
            array(
                'permission_id' => 14,
                'role_id' => 1,
            ),
            14 =>
            array(
                'permission_id' => 15,
                'role_id' => 1,
            ),
            15 =>
            array(
                'permission_id' => 16,
                'role_id' => 1,
            ),
            16 =>
            array(
                'permission_id' => 17,
                'role_id' => 1,
            ),
            17 =>
            array(
                'permission_id' => 18,
                'role_id' => 1,
            ),
            18 =>
            array(
                'permission_id' => 19,
                'role_id' => 1,
            ),
            19 =>
            array(
                'permission_id' => 20,
                'role_id' => 1,
            ),
            20 =>
            array(
                'permission_id' => 21,
                'role_id' => 1,
            ),
            21 =>
            array(
                'permission_id' => 22,
                'role_id' => 1,
            ),
            22 =>
            array(
                'permission_id' => 23,
                'role_id' => 1,
            ),
            23 =>
            array(
                'permission_id' => 24,
                'role_id' => 1,
            ),
            24 =>
            array(
                'permission_id' => 25,
                'role_id' => 1,
            ),
            25 =>
            array(
                'permission_id' => 26,
                'role_id' => 1,
            ),
            26 =>
            array(
                'permission_id' => 27,
                'role_id' => 1,
            ),
            27 =>
            array(
                'permission_id' => 28,
                'role_id' => 1,
            ),
            28 =>
            array(
                'permission_id' => 29,
                'role_id' => 1,
            ),
            29 =>
            array(
                'permission_id' => 30,
                'role_id' => 1,
            ),
            30 =>
            array(
                'permission_id' => 31,
                'role_id' => 1,
            ),
            31 =>
            array(
                'permission_id' => 32,
                'role_id' => 1,
            ),
            32 =>
            array(
                'permission_id' => 33,
                'role_id' => 1,
            ),
            33 =>
            array(
                'permission_id' => 34,
                'role_id' => 1,
            ),
            34 =>
            array(
                'permission_id' => 35,
                'role_id' => 1,
            ),
            35 =>
            array(
                'permission_id' => 36,
                'role_id' => 1,
            ),
            36 =>
            array(
                'permission_id' => 37,
                'role_id' => 1,
            ),
            37 =>
            array(
                'permission_id' => 38,
                'role_id' => 1,
            ),
            38 =>
            array(
                'permission_id' => 39,
                'role_id' => 1,
            ),
            39 =>
            array(
                'permission_id' => 40,
                'role_id' => 1,
            ),
            40 =>
            array(
                'permission_id' => 41,
                'role_id' => 1,
            ),
            41 =>
            array(
                'permission_id' => 42,
                'role_id' => 1,
            ),
            42 =>
            array(
                'permission_id' => 43,
                'role_id' => 1,
            ),
            43 =>
            array(
                'permission_id' => 44,
                'role_id' => 1,
            ),
            44 =>
            array(
                'permission_id' => 45,
                'role_id' => 1,
            ),
            45 =>
            array(
                'permission_id' => 46,
                'role_id' => 1,
            ),
            46 =>
            array(
                'permission_id' => 47,
                'role_id' => 1,
            ),
            47 =>
            array(
                'permission_id' => 48,
                'role_id' => 1,
            ),
            48 =>
            array(
                'permission_id' => 49,
                'role_id' => 1,
            ),
            49 =>
            array(
                'permission_id' => 50,
                'role_id' => 1,
            ),
            50 =>
            array(
                'permission_id' => 51,
                'role_id' => 1,
            ),
            51 =>
            array(
                'permission_id' => 52,
                'role_id' => 1,
            ),
            52 =>
            array(
                'permission_id' => 53,
                'role_id' => 1,
            ),
            53 =>
            array(
                'permission_id' => 54,
                'role_id' => 1,
            ),
            54 =>
            array(
                'permission_id' => 55,
                'role_id' => 1,
            ),
            55 =>
            array(
                'permission_id' => 56,
                'role_id' => 1,
            ),
            56 =>
            array(
                'permission_id' => 57,
                'role_id' => 1,
            ),
            57 =>
            array(
                'permission_id' => 58,
                'role_id' => 1,
            ),
            58 =>
            array(
                'permission_id' => 59,
                'role_id' => 1,
            ),
            59 =>
            array(
                'permission_id' => 60,
                'role_id' => 1,
            ),
            60 =>
            array(
                'permission_id' => 61,
                'role_id' => 1,
            ),
            61 =>
            array(
                'permission_id' => 62,
                'role_id' => 1,
            ),
            62 =>
            array(
                'permission_id' => 63,
                'role_id' => 1,
            ),
            63 =>
            array(
                'permission_id' => 64,
                'role_id' => 1,
            ),
            64 =>
            array(
                'permission_id' => 65,
                'role_id' => 1,
            ),
            65 =>
            array(
                'permission_id' => 66,
                'role_id' => 1,
            ),
            66 =>
            array(
                'permission_id' => 67,
                'role_id' => 1,
            ),
            67 =>
            array(
                'permission_id' => 68,
                'role_id' => 1,
            ),
            68 =>
            array(
                'permission_id' => 69,
                'role_id' => 1,
            ),
            69 =>
            array(
                'permission_id' => 70,
                'role_id' => 1,
            ),
            70 =>
            array(
                'permission_id' => 71,
                'role_id' => 1,
            ),
            71 =>
            array(
                'permission_id' => 72,
                'role_id' => 1,
            ),
            72 =>
            array(
                'permission_id' => 73,
                'role_id' => 1,
            ),
            73 =>
            array(
                'permission_id' => 74,
                'role_id' => 1,
            ),
            74 =>
            array(
                'permission_id' => 75,
                'role_id' => 1,
            ),
            75 =>
            array(
                'permission_id' => 76,
                'role_id' => 1,
            ),
            76 =>
            array(
                'permission_id' => 77,
                'role_id' => 1,
            ),
            77 =>
            array(
                'permission_id' => 78,
                'role_id' => 1,
            ),
            78 =>
            array(
                'permission_id' => 79,
                'role_id' => 1,
            ),
            79 =>
            array(
                'permission_id' => 80,
                'role_id' => 1,
            ),
            80 =>
            array(
                'permission_id' => 81,
                'role_id' => 1,
            ),
            81 =>
            array(
                'permission_id' => 82,
                'role_id' => 1,
            ),
            82 =>
            array(
                'permission_id' => 83,
                'role_id' => 1,
            ),
            83 =>
            array(
                'permission_id' => 84,
                'role_id' => 1,
            ),
            84 =>
            array(
                'permission_id' => 85,
                'role_id' => 1,
            ),
            85 =>
            array(
                'permission_id' => 86,
                'role_id' => 1,
            ),
            86 =>
            array(
                'permission_id' => 87,
                'role_id' => 1,
            ),

            // 87 =>
            // array(
            //     'permission_id' => 88,
            //     'role_id' => 1,
            // ),
            // 88 =>
            // array(
            //     'permission_id' => 89,
            //     'role_id' => 1,
            // ),
            // 89 =>
            // array(
            //     'permission_id' => 90,
            //     'role_id' => 1,
            // ),
            // 90 =>
            // array(
            //     'permission_id' => 91,
            //     'role_id' => 1,
            // ),
            // 91 =>
            // array(
            //     'permission_id' => 92,
            //     'role_id' => 4,
            // ),
            // 92 =>
            // array(
            //     'permission_id' => 93,
            //     'role_id' => 4,
            // ),
            // 93 =>
            // array(
            //     'permission_id' => 94,
            //     'role_id' => 4,
            // ),
            // 94 =>
            // array(
            //     'permission_id' => 95,
            //     'role_id' => 4,
            // ),
            // 95 =>
            // array(
            //     'permission_id' => 96,
            //     'role_id' => 4,
            // ),
            // 96 =>
            // array(
            //     'permission_id' => 97,
            //     'role_id' => 4,
            // ),
            // 97 =>
            // array(
            //     'permission_id' => 98,
            //     'role_id' => 4,
            // ),
            // 98 =>
            // array(
            //     'permission_id' => 99,
            //     'role_id' => 4,
            // ),
            // 99 =>
            // array(
            //     'permission_id' => 100,
            //     'role_id' => 4,
            // ),
            // 100 =>
            // array(
            //     'permission_id' => 101,
            //     'role_id' => 4,
            // ),
            // 101 =>
            // array(
            //     'permission_id' => 102,
            //     'role_id' => 4,
            // ),
            // 102 =>
            // array(
            //     'permission_id' => 103,
            //     'role_id' => 4,
            // ),
            // 103 =>
            // array(
            //     'permission_id' => 104,
            //     'role_id' => 4,
            // ),
            // 104 =>
            // array(
            //     'permission_id' => 105,
            //     'role_id' => 4,
            // ),
            // 105 =>
            // array(
            //     'permission_id' => 106,
            //     'role_id' => 4,
            // ),
            // 106 =>
            // array(
            //     'permission_id' => 107,
            //     'role_id' => 4,
            // ),
            // 107 =>
            // array(
            //     'permission_id' => 108,
            //     'role_id' => 1,
            // ),
            // 108 =>
            // array(
            //     'permission_id' => 109,
            //     'role_id' => 1,
            // ),
            // 81 =>
            // array(
            //     'permission_id' => 82,
            //     'role_id' => 1,
            // ),
            // 110 =>
            // array(
            //     'permission_id' => 110,
            //     'role_id' => 4,
            // ),
            // 111 =>
            // array(
            //     'permission_id' => 110,
            //     'role_id' => 2,
            // ),
            // 112 =>
            // array(
            //     'permission_id' => 54,
            //     'role_id' => 2,
            // ),
            // 113 =>
            // array(
            //     'permission_id' => 56,
            //     'role_id' => 2,
            // ),
            // 114 =>
            // array(
            //     'permission_id' => 58,
            //     'role_id' => 2,
            // ),
            // 115 =>
            // array(
            //     'permission_id' => 55,
            //     'role_id' => 2,
            // ),
            // 116 =>
            // array(
            //     'permission_id' => 54,
            //     'role_id' => 4,
            // ),
            // 117 =>
            // array(
            //     'permission_id' => 56,
            //     'role_id' => 4,
            // ),
            // 118 =>
            // array(
            //     'permission_id' => 58,
            //     'role_id' => 4,
            // ),
            // 119 =>
            // array(
            //     'permission_id' => 55,
            //     'role_id' => 4,
            // ),
            // 120 =>
            // array(
            //     'permission_id' => 111,
            //     'role_id' => 4,
            // ),
            // 121 =>
            // array(
            //     'permission_id' => 111,
            //     'role_id' => 2,
            // ),
            // 122 =>
            // array(
            //     'permission_id' => 60,
            //     'role_id' => 2,
            // ),
            // 123 =>
            // array(
            //     'permission_id' => 60,
            //     'role_id' => 4,
            // ),
            // 124 =>
            // array (
            //     'permission_id' => 112,
            //     'role_id' => 1,
            // ),
            // 125 =>
            // array (
            //     'permission_id' => 113,
            //     'role_id' => 1,
            // ),
            // 126 =>
            // array (
            //     'permission_id' => 114,
            //     'role_id' => 1,
            // ),
            // 128 =>
            // array (
            //     'permission_id' => 115,
            //     'role_id' => 1,
            // ),
            // 129 =>
            // array (
            //     'permission_id' => 116,
            //     'role_id' => 1,
            // ),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
