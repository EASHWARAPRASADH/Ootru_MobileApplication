<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('review_user_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->double('rating')->default('0');
                $table->text('comment')->nullable();
                $table->string('rating_by')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('review_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->timestamps();
            });
        } else {
            Schema::table('ratings', function (Blueprint $table) {
                if (!Schema::hasColumn('ratings', 'review_user_id')) {
                    $table->unsignedBigInteger('review_user_id')->nullable()->after('user_id');
                    $table->foreign('review_user_id')->references('id')->on('users')->onDelete('cascade');
                }

                if (!Schema::hasColumn('ratings', 'comment')) {
                    $table->text('comment')->nullable()->after('rating');
                }

                if (!Schema::hasColumn('ratings', 'rating_by')) {
                    $table->string('rating_by')->nullable()->after('comment');
                }
            });
        }

        Schema::table('orders',function(Blueprint $table){
            if (!Schema::hasColumn('orders', 'is_reschedule')) {
            $table->unsignedBigInteger('is_reschedule')->nullable();
            $table->foreign('is_reschedule')->references('id')->on('reschedules')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
