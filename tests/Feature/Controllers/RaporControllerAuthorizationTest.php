<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\AspekPenilaian;

/**
 * Feature Test: RaporController Authorization
 *
 * Memastikan:
 * - [S-1] IDOR protection: guru tidak bisa akses siswa kelas lain
 * - [S-2] store() validasi kepemilikan siswa_id
 * - Guru yang berhak bisa akses siswa kelasnya sendiri
 *
 * @see \App\Http\Controllers\RaporController
 */
class RaporControllerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $guruA;
    private User $guruB;
    private Kelas $kelasA;
    private Kelas $kelasB;
    private Siswa $siswaA; // milik kelas A (guru A)
    private Siswa $siswaB; // milik kelas B (guru B)

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    private function setUpFixtures(): void
    {
        // Buat Guru A dengan User
        $userA    = User::factory()->create(['role' => 'guru']);
        $guruRecA = Guru::create([
            'user_id'      => $userA->id,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir'=> '1985-01-01',
            'no_hp'        => '08111111',
            'alamat'       => 'Jl. A No.1',
            'jabatan'      => 'Pendidik',
        ]);
        $this->guruA = $userA;

        // Buat Guru B dengan User
        $userB    = User::factory()->create(['role' => 'guru']);
        $guruRecB = Guru::create([
            'user_id'      => $userB->id,
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir'=> '1990-05-15',
            'no_hp'        => '08222222',
            'alamat'       => 'Jl. B No.2',
            'jabatan'      => 'Pendidik',
        ]);
        $this->guruB = $userB;

        // Kelas A → Guru A
        $this->kelasA = Kelas::create(['nama_kelas' => 'TK A Matahari', 'kelas' => 'A', 'guru_id' => $guruRecA->id]);
        // Kelas B → Guru B
        $this->kelasB = Kelas::create(['nama_kelas' => 'TK B Bulan',    'kelas' => 'B', 'guru_id' => $guruRecB->id]);

        // Jadwal: Guru A → Kelas A
        Jadwal::create([
            'guru_id'  => $guruRecA->id,
            'kelas_id' => $this->kelasA->id,
            'hari'     => 'Senin',
        ]);

        // Jadwal: Guru B → Kelas B
        Jadwal::create([
            'guru_id'  => $guruRecB->id,
            'kelas_id' => $this->kelasB->id,
            'hari'     => 'Senin',
        ]);

        // Siswa milik kelas A
        $this->siswaA = Siswa::create([
            'nama'          => 'Siswa A',
            'nis'           => '1001',
            'kelas_id'      => $this->kelasA->id,
            'jenis_kelamin' => 'Laki-Laki',
            'tanggal_lahir' => '2020-01-01',
        ]);

        // Siswa milik kelas B
        $this->siswaB = Siswa::create([
            'nama'          => 'Siswa B',
            'nis'           => '1002',
            'kelas_id'      => $this->kelasB->id,
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2020-05-05',
        ]);
    }

    // ================================================================
    // Test: pilihKelas() — hanya tampilkan kelas milik guru sendiri
    // ================================================================

    public function test_pilih_kelas_hanya_tampilkan_kelas_sendiri(): void
    {
        $response = $this->actingAs($this->guruA)
            ->get(route('guru.rapor.pilih_kelas'));

        $response->assertStatus(200);
        $response->assertSee('TK A Matahari');
        $response->assertDontSee('TK B Bulan');
    }

    public function test_pilih_kelas_guru_b_tidak_lihat_kelas_a(): void
    {
        $response = $this->actingAs($this->guruB)
            ->get(route('guru.rapor.pilih_kelas'));

        $response->assertStatus(200);
        $response->assertSee('TK B Bulan');
        $response->assertDontSee('TK A Matahari');
    }

    // ================================================================
    // Test: daftarSiswa() — guard akses kelas bukan milik guru
    // ================================================================

    public function test_daftar_siswa_guru_bisa_akses_kelas_sendiri(): void
    {
        $response = $this->actingAs($this->guruA)
            ->get(route('guru.rapor.daftar_siswa', $this->kelasA->id));

        $response->assertStatus(200);
    }

    public function test_daftar_siswa_guru_a_diblokir_dari_kelas_b(): void
    {
        // [S-1] IDOR: Guru A tidak boleh akses kelas Guru B via URL manipulation
        $response = $this->actingAs($this->guruA)
            ->get(route('guru.rapor.daftar_siswa', $this->kelasB->id));

        $response->assertStatus(403);
    }

    public function test_daftar_siswa_guru_b_diblokir_dari_kelas_a(): void
    {
        $response = $this->actingAs($this->guruB)
            ->get(route('guru.rapor.daftar_siswa', $this->kelasA->id));

        $response->assertStatus(403);
    }

    // ================================================================
    // Test: detailRapor() — guard akses siswa bukan milik kelas guru
    // ================================================================

    public function test_detail_rapor_guru_bisa_akses_siswa_sendiri(): void
    {
        $response = $this->actingAs($this->guruA)
            ->get(route('guru.rapor.detail', $this->siswaA->id));

        $response->assertStatus(200);
    }

    public function test_detail_rapor_guru_a_diblokir_dari_siswa_b(): void
    {
        // [S-1] IDOR: Guru A tidak boleh akses detail siswa milik Guru B
        $response = $this->actingAs($this->guruA)
            ->get(route('guru.rapor.detail', $this->siswaB->id));

        $response->assertStatus(403);
    }

    public function test_detail_rapor_guru_b_diblokir_dari_siswa_a(): void
    {
        $response = $this->actingAs($this->guruB)
            ->get(route('guru.rapor.detail', $this->siswaA->id));

        $response->assertStatus(403);
    }

    // ================================================================
    // Test: store() — validasi kepemilikan siswa_id via POST
    // ================================================================

    public function test_store_ditolak_jika_siswa_bukan_milik_guru(): void
    {
        // [S-2] Guru A tidak boleh menyimpan nilai untuk siswa B via form manipulation
        $aspek = AspekPenilaian::create([
            'lingkup'      => 'Kognitif',
            'sub_lingkup'  => 'sub-test',
            'indikator'    => 'indikator-test',
        ]);

        $response = $this->actingAs($this->guruA)
            ->post(route('guru.rapor.store'), [
                'siswa_id'      => $this->siswaB->id, // Siswa milik Guru B!
                'periode'       => 'Ganjil',
                'tahun_ajaran'  => '2026/2027',
                'nilai'         => [$aspek->id => 3],
            ]);

        $response->assertStatus(403);
    }

    public function test_store_diterima_jika_siswa_milik_guru(): void
    {
        $aspek = AspekPenilaian::create([
            'lingkup'      => 'Kognitif',
            'sub_lingkup'  => 'sub-test',
            'indikator'    => 'indikator-test',
        ]);

        $response = $this->actingAs($this->guruA)
            ->post(route('guru.rapor.store'), [
                'siswa_id'      => $this->siswaA->id, // Siswa milik Guru A ✓
                'periode'       => 'Ganjil',
                'tahun_ajaran'  => '2026/2027',
                'nilai'         => [$aspek->id => 3],
            ]);

        // Harus redirect ke halaman detail (bukan 403)
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    // ================================================================
    // Test: Unauthenticated access redirect
    // ================================================================

    public function test_unauthenticated_user_diredirect_ke_login(): void
    {
        $response = $this->get(route('guru.rapor.pilih_kelas'));
        $response->assertRedirect(route('login'));
    }

    // ================================================================
    // Test: generateAnalisis() — IDOR guard kelas
    // ================================================================

    public function test_generate_analisis_ditolak_jika_kelas_bukan_milik_guru(): void
    {
        // [S-KELAS] Guru A tidak boleh trigger analisis untuk kelas B
        $response = $this->actingAs($this->guruA)
            ->post(route('guru.rapor.analisis'), [
                'kelas_id'    => $this->kelasB->id,  // kelas milik Guru B!
                'periode'     => 'Ganjil',
                'tahun_ajaran'=> '2026/2027',
            ]);

        $response->assertStatus(403);
    }

    public function test_generate_analisis_diterima_jika_kelas_milik_guru(): void
    {
        // Guru A boleh trigger analisis untuk kelas A (meski akan gagal karena < 6 siswa)
        $response = $this->actingAs($this->guruA)
            ->post(route('guru.rapor.analisis'), [
                'kelas_id'    => $this->kelasA->id,  // kelas milik Guru A ✓
                'periode'     => 'Ganjil',
                'tahun_ajaran'=> '2026/2027',
            ]);

        // Tidak boleh 403 — bisa redirect dengan error (siswa < 6) tapi bukan Forbidden
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionMissing('success');  // gagal karena data kurang, bukan forbidden
    }
}
