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
        if (Schema::hasTable('book_address')) {
            Schema::create('book_address', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('address_default', 100);
                $table->json('address_list')->default('[]');
                $table->string('phone', 50);
                $table->enum('address_type', ['company', 'house', 'other']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Dùng để xóa bảng
    {
        Schema::dropIfExists('book_address');
    }
};
