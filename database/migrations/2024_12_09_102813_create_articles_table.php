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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('code_article');
            $table->string('unite');
            $table->integer('cls');
            $table->integer('cls2');
            $table->string('ref');
            $table->string('designation');
            $table->string('code_abc')->nullable();
            $table->string('designation_abc')->nullable();
            $table->string('PRODH')->nullable();
            $table->string('VTEXT')->nullable();
            $table->string('MVGR1')->nullable();
            $table->string('BEZEI')->nullable();
            $table->string('MVGR2')->nullable();
            $table->string('BEZE2')->nullable();
            $table->string('MVGR3')->nullable();
            $table->string('BEZE3')->nullable();
            $table->string('MVGR4')->nullable();
            $table->string('BEZE4')->nullable();
            $table->string('VMSTA')->nullable();
            $table->string('VMSTD')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
