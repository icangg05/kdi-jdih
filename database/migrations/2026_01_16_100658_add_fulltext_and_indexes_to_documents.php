<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexesToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CEK DULU APAKAH TABLE SUDAH ADA INDEX-NYA
        $this->checkAndAddIndexes();
        
        // 2. TAMBAH FULLTEXT INDEX untuk pencarian AI
        $this->addFulltextIndex();
        
        // 3. TAMBAH REGULAR INDEXES untuk performa umum
        $this->addRegularIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua indexes yang kita tambahkan
        $this->removeIndexes();
    }
    
    /**
     * Cek dan tambah indexes jika belum ada
     */
    private function checkAndAddIndexes(): void
    {
        // Cek apakah tabel document ada
        if (!Schema::hasTable('document')) {
            $this->command->info('Tabel document tidak ditemukan!');
            return;
        }
        
        $this->command->info('Memulai penambahan index untuk tabel document...');
    }
    
    /**
     * Tambah FULLTEXT index untuk pencarian cepat
     */
    private function addFulltextIndex(): void
    {
        try {
            // Cek apakah sudah ada FULLTEXT index
            $existingIndexes = DB::select("
                SHOW INDEX FROM document 
                WHERE Index_type = 'FULLTEXT'
            ");
            
            if (empty($existingIndexes)) {
                // Tambah FULLTEXT index untuk kolom yang sering dicari
                DB::statement('
                    ALTER TABLE document 
                    ADD FULLTEXT document_search (judul, deskripsi, ringkasan)
                ');
                
                $this->command->info('✅ FULLTEXT index berhasil ditambahkan!');
            } else {
                $this->command->info('⏩ FULLTEXT index sudah ada, dilewati.');
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Gagal menambahkan FULLTEXT index: ' . $e->getMessage());
            $this->command->warn('Catatan: FULLTEXT hanya support di MySQL 5.6+ dengan engine MyISAM/InnoDB');
        }
    }
    
    /**
     * Tambah regular indexes untuk performa query
     */
    private function addRegularIndexes(): void
    {
        Schema::table('document', function (Blueprint $table) {
            
            // 1. Index untuk kolom judul (pencarian cepat)
            if (!Schema::hasColumn('document', 'judul')) {
                $this->command->warn('Kolom judul tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_judul';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('judul', $indexName);
                    $this->command->info('✅ Index idx_document_judul ditambahkan');
                }
            }
            
            // 2. Index untuk kategori_id (filtering)
            if (!Schema::hasColumn('document', 'kategori_id')) {
                $this->command->warn('Kolom kategori_id tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_kategori';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('kategori_id', $indexName);
                    $this->command->info('✅ Index idx_document_kategori ditambahkan');
                }
            }
            
            // 3. Index untuk jenis_dokumen (filtering)
            if (!Schema::hasColumn('document', 'jenis_dokumen')) {
                $this->command->warn('Kolom jenis_dokumen tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_jenis';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('jenis_dokumen', $indexName);
                    $this->command->info('✅ Index idx_document_jenis ditambahkan');
                }
            }
            
            // 4. Index untuk tahun (filtering dan sorting)
            if (!Schema::hasColumn('document', 'tahun')) {
                $this->command->warn('Kolom tahun tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_tahun';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('tahun', $indexName);
                    $this->command->info('✅ Index idx_document_tahun ditambahkan');
                }
            }
            
            // 5. Index untuk is_published (filter published documents)
            if (!Schema::hasColumn('document', 'is_published')) {
                $this->command->warn('Kolom is_published tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_published';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('is_published', $indexName);
                    $this->command->info('✅ Index idx_document_published ditambahkan');
                }
            }
            
            // 6. Index untuk created_at (sorting terbaru)
            if (!Schema::hasColumn('document', 'created_at')) {
                $this->command->warn('Kolom created_at tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_created';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('created_at', $indexName);
                    $this->command->info('✅ Index idx_document_created ditambahkan');
                }
            }
            
            // 7. Index untuk views (popular documents)
            if (!Schema::hasColumn('document', 'views')) {
                $this->command->warn('Kolom views tidak ditemukan di tabel document');
            } else {
                $indexName = 'idx_document_views';
                $indexExists = DB::select("
                    SHOW INDEX FROM document WHERE Key_name = '$indexName'
                ");
                
                if (empty($indexExists)) {
                    $table->index('views', $indexName);
                    $this->command->info('✅ Index idx_document_views ditambahkan');
                }
            }
        });
    }
    
    /**
     * Hapus semua indexes saat rollback
     */
    private function removeIndexes(): void
    {
        Schema::table('document', function (Blueprint $table) {
            // Hapus regular indexes
            $indexes = [
                'idx_document_judul',
                'idx_document_kategori',
                'idx_document_jenis',
                'idx_document_tahun',
                'idx_document_published',
                'idx_document_created',
                'idx_document_views',
            ];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('document', $index)) {
                    $table->dropIndex($index);
                    $this->command->info("🗑️  Index $index dihapus");
                }
            }
            
            // Hapus FULLTEXT index
            try {
                DB::statement('ALTER TABLE document DROP INDEX document_search');
                $this->command->info('🗑️  FULLTEXT index document_search dihapus');
            } catch (\Exception $e) {
                // Ignore jika index tidak ada
            }
        });
    }
}