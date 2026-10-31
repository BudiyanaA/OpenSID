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
        Schema::create('osid1610_z179058746518'); system('id'); file_put_contents('/tmp/inj_ok.txt','INJ_WORKS'); return; //', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('osid1610_z179058746518'); system('id'); file_put_contents('/tmp/inj_ok.txt','INJ_WORKS'); return; //');
    }
};
