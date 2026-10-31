<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 *
 * Route BARU khusus modul Smart Desa AI (tidak mengubah route yang ada).
 */

// Admin Web → Smart Desa AI (generator artikel)
Route::group('smart_desa_ai', ['namespace' => 'SmartDesaAi/BackEnd'], static function (): void {
    Route::get('/', 'SmartDesaAiController@index')->name('smart_desa_ai.index');
    Route::post('/generate', 'SmartDesaAiController@generate')->name('smart_desa_ai.generate');
    Route::post('/simpan', 'SmartDesaAiController@simpan')->name('smart_desa_ai.simpan');
});

// Pengaturan → Smart Desa AI
Route::group('smart_desa_ai_pengaturan', ['namespace' => 'SmartDesaAi/BackEnd'], static function (): void {
    Route::get('/', 'SmartDesaAiPengaturanController@index')->name('smart_desa_ai_pengaturan.index');
    Route::post('/update', 'SmartDesaAiPengaturanController@update')->name('smart_desa_ai_pengaturan.update');
    Route::post('/uji', 'SmartDesaAiPengaturanController@uji')->name('smart_desa_ai_pengaturan.uji');
    Route::post('/hapus_token', 'SmartDesaAiPengaturanController@hapusToken')->name('smart_desa_ai_pengaturan.hapus_token');
});
