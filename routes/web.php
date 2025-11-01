<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;

Route::get('/dokumen', [DokumenController::class, 'indexPage']);      // ⬅️ halaman tabel
Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])  // ⬅️ data JSON
     ->name('dokumen.data');

Route::get('/dokumen/{id}', [DokumenController::class, 'show']);      // detail JSON (opsional)

// Health check tetap ok
Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");
    return response()->json(['ok'=>true,'db'=>$row->db??null,'user'=>$row->u??null,'time'=>$row->ts??null]);
});
