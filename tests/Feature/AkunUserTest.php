<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

// Kolom password di tabel user bernama password_hash, dan akun warisan memakai cost bcrypt berbeda.
class AkunUserTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // Kunci ke SQLite in-memory: env Docker (DB_DATABASE) tidak ditimpa phpunit.xml,
    // dan migration lengkap butuh tabel legacy MySQL, jadi tabelnya dibuat di sini saja.
    config([
      'database.default' => 'sqlite',
      'database.connections.sqlite.database' => ':memory:',
      'session.driver' => 'array',
    ]);
    DB::purge('sqlite');

    Schema::create('user', function (Blueprint $table) {
      $table->id();
      $table->string('username');
      $table->string('auth_key')->nullable();
      $table->string('password_hash');
      $table->string('email');
      $table->integer('status')->default(10);
      $table->string('picture')->nullable();
      $table->integer('created_at')->nullable();
      $table->integer('updated_at')->nullable();
    });
  }

  private function akun(string $hash, string $username = 'lama'): User
  {
    return User::create([
      'username'      => $username,
      'email'         => $username . '@example.test',
      'password_hash' => $hash,
      'status'        => 10,
    ]);
  }

  public function test_akun_dengan_hash_lama_bisa_login_dan_hashnya_diperbarui(): void
  {
    $hashLama = password_hash('rahasia123', PASSWORD_BCRYPT, ['cost' => 5]);
    $user     = $this->akun($hashLama);

    $this->post(route('authenticate'), ['username' => 'lama', 'password' => 'rahasia123'])
      ->assertRedirect(route('backend.dashboard'));

    $hashBaru = $user->fresh()->password_hash;
    $this->assertNotSame($hashLama, $hashBaru);
    $this->assertTrue(Hash::check('rahasia123', $hashBaru));
  }

  public function test_edit_user_tanpa_password_tidak_mengubah_hash(): void
  {
    $hash = Hash::make('rahasia123');
    $user = $this->akun($hash);

    $this->actingAs($user)
      ->patch(route('backend.user.update', $user->id), ['username' => 'baru', 'email' => 'baru@example.test'])
      ->assertRedirect(route('backend.user.index'));

    $user->refresh();
    $this->assertSame('baru', $user->username);
    $this->assertSame($hash, $user->password_hash);
  }

  public function test_edit_user_dengan_password_mengganti_hash(): void
  {
    $user = $this->akun(Hash::make('rahasia123'));

    $this->actingAs($user)
      ->patch(route('backend.user.update', $user->id), [
        'username'              => 'lama',
        'email'                 => 'lama@example.test',
        'password'              => 'passwordbaru',
        'password_confirmation' => 'passwordbaru',
      ])
      ->assertRedirect(route('backend.user.index'));

    $this->assertTrue(Hash::check('passwordbaru', $user->fresh()->password_hash));
  }

  public function test_halaman_profil_mengarah_ke_detail_user_yang_login(): void
  {
    $user = $this->akun(Hash::make('rahasia123'));

    $this->actingAs($user)
      ->get(route('backend.profil'))
      ->assertRedirect(route('backend.user.show', $user->id));
  }
}
