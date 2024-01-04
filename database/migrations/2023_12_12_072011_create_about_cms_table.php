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
        Schema::create('about_cms', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('number_of_project')->nullable();
            $table->string('programming_language_known')->nullable();
            $table->string('framework_known')->nullable();
            $table->string('client_handled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_cms');
    }
};
