<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\SmartDesaAi\Models\SmartDesaAiLog;

return new class () extends Migration {
    /**
     * Tabel log pemakaian. Idempoten: aman dijalankan berulang.
     */
    public function up(): void
    {
        if (Schema::hasTable('smart_desa_ai_log')) {
            return;
        }

        Schema::create('smart_desa_ai_log', static function (Blueprint $table) {
            $table->increments('id');
            $table->configId();
            $table->integer('user_id')->nullable();
            $table->string('aksi', 20);
            $table->string('provider', 20)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('jenis_artikel', 50)->nullable();
            $table->string('status', 10);
            $table->string('kode_error', 40)->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->unsignedInteger('durasi_ms')->nullable();
            $table->integer('artikel_id')->nullable();
            $table->timestamps();

            $table->index(['config_id', 'aksi', 'created_at'], 'smart_desa_ai_log_pemakaian');
            $table->index(['config_id', 'user_id', 'created_at'], 'smart_desa_ai_log_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExistsDBGabungan('smart_desa_ai_log', static function () {
            SmartDesaAiLog::withoutConfigId(identitas('id'))->delete();
        });
    }
};
