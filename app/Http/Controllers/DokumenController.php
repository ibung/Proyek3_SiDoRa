<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    // GET /api/dokumen?q=&status=&kategori_id=
    public function index(Request $r)
    {
        $q = Dokumen::with(['kategori','creator'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function($x) use ($r) {
                    $x->where('judul','ilike','%'.$r->q.'%')
                      ->orWhere('nomor_dokumen','ilike','%'.$r->q.'%');
                })
            )
            ->when($r->filled('status'), fn($w) => $w->where('status', $r->status))
            ->when($r->filled('kategori_id'), fn($w) => $w->where('kategori_id', $r->kategori_id))
            ->orderByDesc('dokumen_id');

        return $q->paginate(10);
    }

    // GET /api/dokumen/{id}
    public function show($id)
    {
        $doc = Dokumen::with(['kategori','creator','komentar.user','versi'])
            ->where('dokumen_id', $id)
            ->firstOrFail();

        return $doc;
    }

    public function indexPage(\Illuminate\Http\Request $r)
    {
        $kategori = \App\Models\Kategori::select('kategori_id','nama_kategori')
            ->orderBy('nama_kategori')->get();

        return view('dokumen.index', compact('kategori'));
    }

    public function indexJson(\Illuminate\Http\Request $r)
    {
        $q = \App\Models\Dokumen::with(['kategori','creator'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function($x) use ($r) {
                    $x->where('judul','ilike','%'.$r->q.'%')
                    ->orWhere('nomor_dokumen','ilike','%'.$r->q.'%');
                })
            )
            ->when($r->filled('status'), fn($w) => $w->where('status', $r->status))
            ->when($r->filled('kategori_id'), fn($w) => $w->where('kategori_id', $r->kategori_id))
            ->orderByDesc('dokumen_id');

        return response()->json($q->paginate($r->integer('per_page', 10)));
    }
}
