<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'komentar';
    protected $primaryKey = 'komentar_id';
    public $timestamps = false; // kita pakai 'tanggal_komentar' sendiri
    protected $fillable = ['idUser','dokumen_id','isi_komentar','tanggal_komentar'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'idUser');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }
}
