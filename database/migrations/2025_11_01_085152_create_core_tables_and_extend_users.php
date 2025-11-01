<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom ke tabel users bawaan Laravel
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'dosen', 'staff'])->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true)->after('role');
            }
        });

        // Tabel kategori
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('kategori_id');
            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel dokumen
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('dokumen_id');
            $table->string('judul', 255);
            $table->string('nomor_dokumen', 50)->nullable();
            $table->date('tanggal_terbit')->nullable();

            $table->foreignId('kategori_id')->nullable()
                ->constrained('kategori', 'kategori_id')
                ->nullOnDelete();

            $table->text('file_path')->nullable();
            $table->text('deskripsi')->nullable();

            // created_by -> refer ke users.id
            $table->foreignId('created_by')->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->enum('status', ['draft','publik','arsip'])->nullable();

            $table->timestamps(); // created_at & updated_at
        });

        // Tabel komentar (pakai nama kolom idUser sesuai dummy-mu)
        Schema::create('komentar', function (Blueprint $table) {
            $table->id('komentar_id');

            $table->foreignId('idUser') // refer ke users.id
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            $table->foreignId('dokumen_id')
                ->constrained('dokumen', 'dokumen_id')
                ->cascadeOnDelete();

            $table->text('isi_komentar');
            $table->timestamp('tanggal_komentar')->useCurrent();
        });

        // Tabel versi_dokumen
        Schema::create('versi_dokumen', function (Blueprint $table) {
            $table->id('versi_id');
            $table->foreignId('dokumen_id')
                ->constrained('dokumen', 'dokumen_id')
                ->cascadeOnDelete();

            $table->string('nomor_versi', 20)->nullable();
            $table->text('file_path')->nullable();
            $table->date('tanggal_dokumen')->nullable();

            // upload_by -> refer ke users.id
            $table->foreignId('upload_by')->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versi_dokumen');
        Schema::dropIfExists('komentar');
        Schema::dropIfExists('dokumen');
        Schema::dropIfExists('kategori');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->dropColumn('nama_lengkap');
            }
        });
    }
};
