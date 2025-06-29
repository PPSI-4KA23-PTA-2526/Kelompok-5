<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesanKontak;
use App\Mail\KontakMasukMail;
use Illuminate\Support\Facades\Mail;

class KontakController extends Controller
{
    public function kirim(Request $request)
{
    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email',
        'telepon' => 'nullable|string|max:20',
        'pesan' => 'required|string',
    ]);

    // dd($validated); <-- Ini komentar dulu ya

    PesanKontak::create($validated);

    // Kirim ke Gmail kamu
    Mail::to('ngibran07@gmail.com')->send(new KontakMasukMail($validated));

    return back()->with('success', 'Terima kasih, Pesan Anda telah berhasil dikirim.');
}

}

