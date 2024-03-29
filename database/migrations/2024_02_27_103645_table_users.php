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
        if (Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('book_address_id')->nullable();
                $table->string('avatar', 100)->nullable();
                $table->string('image_cover', 100)->nullable();
                $table->string('username', 50);
                $table->string('first_name', 50);
                $table->string('last_name', 50);
                $table->date('birthday');
                $table->string('email', 50);
                $table->string('password', 150);
                $table->string('address', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('country', 100)->nullable();
                $table->string('phone', 50)->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->enum('gender', ['man', 'woman', 'other'])->nullable();
                $table->enum('role', ['user', 'member', 'admin'])->default('user');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    { 
        Schema::dropIfExists('users'); 
    }
};
