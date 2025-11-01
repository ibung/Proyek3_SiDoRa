<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users dummy ---
        // Map dummy kamu: nama_lengkap + role; password dibikin hash yang bener
        $users = [
            ['nama_lengkap' => 'Ahmad Yusuf', 'email' => 'yusuf@example.com', 'role' => 'admin', 'password' => 'hashedpass1'],
            ['nama_lengkap' => 'Dewi Lestari', 'email' => 'dewi@example.com',  'role' => 'dosen', 'password' => 'hashedpass2'],
            ['nama_lengkap' => 'Budi Santoso', 'email' => 'budi@example.com',  'role' => 'staff', 'password' => 'hashedpass3'],
        ];

        foreach ($users as $u) {
            // isi juga kolom 'name' bawaan Laravel agar fitur auth senang
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                [
                    'name'         => $u['nama_lengkap'],
                    'nama_lengkap' => $u['nama_lengkap'],
                    'email'        => $u['email'],
                    'role'         => $u['role'],
                    'status'       => true,
                    'password'     => Hash::make($u['password']),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }

        // --- Kategori dummy ---
        $kategoriIds = [];
        $kategoriData = [
            ['nama_kategori' => 'Surat Keputusan', 'deskripsi' => 'Dokumen resmi keputusan pimpinan'],
            ['nama_kategori' => 'Notulen',         'deskripsi' => 'Catatan hasil rapat'],
            ['nama_kategori' => 'Laporan',         'deskripsi' => 'Laporan kegiatan tahunan'],
        ];
        foreach ($kategoriData as $row) {
            $id = DB::table('kategori')->insertGetId([
                'nama_kategori' => $row['nama_kategori'],
                'deskripsi'     => $row['deskripsi'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ], 'kategori_id');
        }

        // Ambil user id untuk referensi
        $u1 = DB::table('users')->where('email','yusuf@example.com')->value('id');
        $u2 = DB::table('users')->where('email','dewi@example.com')->value('id');

        // --- Dokumen dummy ---
        $d1 = DB::table('dokumen')->insertGetId([
            'judul'          => 'SK Pengangkatan Dosen',
            'nomor_dokumen'  => 'SK-2025-001',
            'tanggal_terbit' => '2025-01-15',
            'kategori_id'    => $kategoriIds[0] ?? null,
            'file_path'      => '/files/sk_pengangkatan.pdf',
            'deskripsi'      => 'Surat keputusan dosen baru',
            'created_by'     => $u1,
            'status'         => 'publik',
            'created_at'     => now(),
            'updated_at'     => now(),
        ], 'dokumen_id');

        $d2 = DB::table('dokumen')->insertGetId([
            'judul'          => 'Notulen Rapat Fakultas',
            'nomor_dokumen'  => 'NT-2025-002',
            'tanggal_terbit' => '2025-03-10',
            'kategori_id'    => $kategoriIds[1] ?? null,
            'file_path'      => '/files/notulen_fakultas.pdf',
            'deskripsi'      => 'Rapat koordinasi semester genap',
            'created_by'     => $u2,
            'status'         => 'draft',
            'created_at'     => now(),
            'updated_at'     => now(),
        ], 'dokumen_id');

        // --- Komentar dummy ---
        DB::table('komentar')->insert([
            [
                'idUser'          => $u2,
                'dokumen_id'      => $d1,
                'isi_komentar'    => 'Mohon periksa kembali tanda tangan.',
                'tanggal_komentar'=> now(),
            ],
            [
                'idUser'          => DB::table('users')->where('email','budi@example.com')->value('id'),
                'dokumen_id'      => $d2,
                'isi_komentar'    => 'Format notulen sudah sesuai.',
                'tanggal_komentar'=> now(),
            ],
        ]);

        // --- Versi dokumen dummy ---
        DB::table('versi_dokumen')->insert([
            [
                'dokumen_id'      => $d1,
                'nomor_versi'     => 'v1.0',
                'file_path'       => '/files/sk_pengangkatan_v1.pdf',
                'tanggal_dokumen' => '2025-01-10',
                'upload_by'       => $u1,
            ],
            [
                'dokumen_id'      => $d1,
                'nomor_versi'     => 'v1.1',
                'file_path'       => '/files/sk_pengangkatan_v1_1.pdf',
                'tanggal_dokumen' => '2025-01-13',
                'upload_by'       => $u1,
            ],
            [
                'dokumen_id'      => $d2,
                'nomor_versi'     => 'v1.0',
                'file_path'       => '/files/notulen_v1.pdf',
                'tanggal_dokumen' => '2025-03-10',
                'upload_by'       => $u2,
            ],
        ]);
    }
}
