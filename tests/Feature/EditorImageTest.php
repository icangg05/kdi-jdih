<?php

namespace Tests\Feature;

use App\Models\Narasi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Memastikan gambar editor Quill tidak menumpuk di storage.
class EditorImageTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    DB::purge('sqlite');

    foreach (['narasi' => 'text', 'berita' => 'isi', 'pengumuman' => 'isi', 'informasi_hukum' => 'isi', 'document' => 'amar_status'] as $table => $column) {
      Schema::create($table, function (Blueprint $t) use ($column) {
        $t->id();
        $t->text($column)->nullable();
        $t->text('translations')->nullable();
      });
    }

    Storage::fake();
    $this->actingAs(new User());
    $this->withoutMiddleware(ValidateCsrfToken::class); // APP_ENV Docker bukan 'testing'
  }

  private function upload(): string
  {
    return $this->post(route('backend.editor.upload-image'), ['image' => UploadedFile::fake()->image('a.png')])
      ->assertOk()->json('url');
  }

  // File berumur 2 hari (lewat masa tenggang).
  private function oldFile(string $name): string
  {
    Storage::put("editor/$name", 'x');
    touch(Storage::path("editor/$name"), time() - 2 * 86400);
    return "editor/$name";
  }

  public function test_hanya_file_lama_yang_tak_dirujuk_yang_terhapus(): void
  {
    $berita   = $this->oldFile('berita.png');
    $terjemah = $this->oldFile('terjemah.png');
    $putusan  = $this->oldFile('narasi/putusan.png'); // subfolder lama ikut dicek
    $yatim    = $this->oldFile('yatim.png');

    DB::table('berita')->insert(['isi' => '<img src="http://x/storage/editor/berita.png">']);
    DB::table('pengumuman')->insert(['isi' => '<p>-</p>', 'translations' => json_encode(['en' => ['isi' => '<img src="http://x/storage/editor/terjemah.png">']])]);
    DB::table('document')->insert(['amar_status' => '<img src="http://x/storage/editor/narasi/putusan.png">']);

    $baru = 'editor/' . basename($this->upload()); // baru & belum dirujuk: aman (form belum disimpan)

    Storage::assertExists([$berita, $terjemah, $putusan, $baru]);
    Storage::assertMissing($yatim);
  }

  public function test_gambar_langsung_terhapus_saat_diupdate_dan_datanya_dihapus(): void
  {
    [$a, $b, $c, $d] = [$this->upload(), $this->upload(), $this->upload(), $this->upload()];
    $path = fn ($url) => 'editor/' . basename($url);

    DB::table('berita')->insert(['isi' => "<img src=\"$c\">"]); // $c juga dipakai berita (salin-tempel)
    $narasi = Narasi::create(['text' => "<img src=\"$a\"><img src=\"$b\"><img src=\"$c\">", 'translations' => json_encode(['en' => ['text' => "<img src=\"$d\">"]])]);

    // $b & $c dibuang dari isi; $d dibuang dari teks tapi masih ada di terjemahan lama.
    $narasi->update(['text' => "<img src=\"$a\">"]);
    Storage::assertMissing($path($b));
    Storage::assertExists([$path($a), $path($c), $path($d)]);

    $narasi->delete();
    Storage::assertMissing([$path($a), $path($d)]);
    Storage::assertExists($path($c));
  }

  public function test_pesan_validasi_jelas(): void
  {
    $kirim = fn ($file) => $this->postJson(route('backend.editor.upload-image'), ['image' => $file])->assertStatus(422)->json('message');

    $this->assertSame('Ukuran gambar maksimal 2 MB.', $kirim(UploadedFile::fake()->image('a.png')->size(3000)));

    // > upload_max_filesize: PHP menolak file sebelum sampai ke validasi ukuran
    $tmp = tempnam(sys_get_temp_dir(), 'img');
    $this->assertStringContainsString('tidak lebih dari 2 MB', $kirim(new UploadedFile($tmp, 'a.png', 'image/png', UPLOAD_ERR_INI_SIZE, true)));

    $this->assertStringContainsString('harus berupa gambar', $kirim(UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')));
  }
}
