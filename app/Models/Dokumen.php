<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';
    protected $primaryKey = 'dokumen_id';
    protected $fillable = [
        'judul','nomor_dokumen','tanggal_terbit','kategori_id',
        'file_path','deskripsi','created_by','status'
    ];
    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'dokumen_id', 'dokumen_id');
    }

    public function versi()
    {
        return $this->hasMany(VersiDokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
