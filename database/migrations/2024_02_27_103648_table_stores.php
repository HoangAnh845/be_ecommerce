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
        if (Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('avatar', 100);
                $table->string('image_cover', 100);
                $table->string('introduce', 255);
                $table->string('type', 255);
                $table->enum('status', ['active', 'lockup', 'close'])->default('active');
                $table->integer('products_total');
                $table->integer('reviews_total');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('stores');
    }
};
