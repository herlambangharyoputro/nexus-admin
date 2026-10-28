<?php

/*
| Workbench (dev-only) — route halaman demo untuk verifikasi visual & Playwright.
| Tidak ikut dimuat consumer (NexusServiceProvider tidak mendaftarkan route).
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Sajikan aset langsung dari resources/assets (pengganti vendor:publish saat dev).
Route::get('/nexus/assets/{path}', function (string $path) {
    $base = realpath(__DIR__ . '/../../resources/assets');
    $file = realpath($base . '/' . $path);
    abort_unless($file && str_starts_with($file, $base) && is_file($file), 404);

    $types = ['css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml', 'png' => 'image/png'];

    return response()->file($file, ['Content-Type' => $types[pathinfo($file, PATHINFO_EXTENSION)] ?? 'application/octet-stream']);
})->where('path', '.*');

Route::middleware('web')->group(function () {
    Route::view('/', 'nexus::pages.demo');
    Route::view('/nexus-demo', 'nexus::pages.demo');
    Route::view('/nexus-demo/index', 'nexus::pages.demo-index');
    Route::view('/nexus-demo/form', 'nexus::pages.demo-form');
    Route::view('/nexus-demo/detail', 'nexus::pages.demo-detail');

    // Alur nyata redirect()->back()->withErrors()->withInput() untuk demo old() & error.
    Route::post('/nexus-demo/form', function (Request $request) {
        $request->validate([
            'kode'      => ['required', 'max:20'],
            'nama'      => ['required'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'kondisi'   => ['required'],
        ]);

        return redirect('/nexus-demo/form')->with('success', 'Lokasi berhasil disimpan.');
    });
});
