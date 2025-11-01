<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersiDokumen extends Model
{
    protected $table = 'versi_dokumen';
    protected $primaryKey = 'versi_id';
    public $timestamps = false;
    protected $fillable = ['dokumen_id','nomor_versi','file_path','tanggal_dokumen','upload_by'];
    protected $casts = ['tanggal_dokumen' => 'date'];

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id', 'dokumen_id');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'upload_by');
    }
}
