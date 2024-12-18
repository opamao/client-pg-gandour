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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('code_client');
            $table->string('precode_client')->nullable();
            $table->string('name_client');
            $table->string('email_client')->nullable();
            $table->string('logo_client')->nullable();
            $table->unsignedBigInteger('pays_id')->nullable();
            $table->foreign('pays_id')->references('id')->on('pays');
            $table->string('last_sync_attempt')->nullable();
            $table->integer('status_client')->comment('0 = on, 1 = off')->nullable();
            $table->string('password_client');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['division_id', 'pays_id']);
            $table->dropColumn('division_id');
            $table->dropColumn('pays_id');
        });
    }
};
