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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('family_id')->nullable()->default(null);
            $table->string('email', 50)->unique();
            $table->string('password')->unique();
            $table->string('phone_no')->unique();
            $table->string('img_url')->nullable()->default(null);
            $table->integer('badget')->nullable()->default(0);
            $table->boolean('blocked')->default(0);
            $table->timestamps();
            $table->foreign('family_id')->references('id')
                ->on('families')->onDelete('cascade');
            $table->foreign('type_id')->references('id')
                ->on('users_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
