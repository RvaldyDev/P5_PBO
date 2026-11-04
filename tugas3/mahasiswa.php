<?php
declare(strict_types=1);

/**
 * Class Mahasiswa dengan State Terenkapsulasi (Terkontrol & Terlindungi)
 * Modul Praktikum 05: Enkapsulasi (Encapsulation)
 */
class Mahasiswa {
    // Properti private: akses langsung dari luar class diblokir demi menjaga integritas data
    private string $nim;
    private string $nama;
    private string $fakultas;
    private string $prodi;
    private string $asalSekolah;
    private int $semester;
    private string $hobi;
    private string $agama;

    // Daftar agama resmi yang sah dan diizinkan
    public const AGAMA_DIZINKAN = [
        'Islam',
        'Kristen',
        'Kristen Protestan',
        'Katolik',
        'Hindu',
        'Buddha',
        'Khonghucu'
    ];

    // Alias untuk kompatibilitas
    public const AGAMA_VALID = self::AGAMA_DIZINKAN;

    public function __construct(
        string $nim,
        string $nama,
        string $fakultas,
        string $prodi,
        string $asalSekolah,
        int $semester,
        string $hobi,
        string $agama
    ) {
        $valNim = trim($nim);
        if ($valNim === '') {
            throw new InvalidArgumentException("NIM wajib diisi dan tidak boleh kosong.");
        }
        $this->nim = $valNim;

        $this->setNama($nama);
        $this->setFakultas($fakultas);
        $this->setProdi($prodi);
        $this->setAsalSekolah($asalSekolah);
        $this->setSemester($semester);
        $this->setHobi($hobi);
        $this->setAgama($agama);
    }

    // ==========================================
    // GETTER (Jalur Pembacaan Sah - Read Only)
    // ==========================================
    public function getNim(): string { return $this->nim; }
    public function getNama(): string { return $this->nama; }
    public function getFakultas(): string { return $this->fakultas; }
    public function getProdi(): string { return $this->prodi; }
    public function getAsalSekolah(): string { return $this->asalSekolah; }
    public function getSemester(): int { return $this->semester; }
    public function getHobi(): string { return $this->hobi; }
    public function getAgama(): string { return $this->agama; }

    // ==========================================
    // SETTER (Gerbang Validasi Aturan Bisnis)
    // Catatan: Tidak ada setNim() karena NIM bersifat Immutable
    // ==========================================
    public function setNama(string $nama): void {
        $val = trim($nama);
        if ($val === '') {
            throw new InvalidArgumentException("Nama mahasiswa tidak boleh kosong.");
        }
        if (mb_strlen($val) < 3) {
            throw new InvalidArgumentException("Nama minimal terdiri dari 3 karakter.");
        }
        $this->nama = $val;
    }

    public function setFakultas(string $fakultas): void {
        $val = trim($fakultas);
        if ($val === '') {
            throw new InvalidArgumentException("Fakultas tidak boleh kosong!");
        }
        $this->fakultas = $val;
    }

    public function setProdi(string $prodi): void {
        $val = trim($prodi);
        if ($val === '') {
            throw new InvalidArgumentException("Program studi tidak boleh kosong!");
        }
        $this->prodi = $val;
    }

    public function setAsalSekolah(string $asalSekolah): void {
        $this->asalSekolah = trim($asalSekolah);
    }

    public function setSemester(int $semester): void {
        if ($semester < 1 || $semester > 14) {
            throw new InvalidArgumentException("Semester tidak valid! Harus bernilai antara 1 sampai 14 (diberikan: $semester).");
        }
        $this->semester = $semester;
    }

    public function setHobi(string $hobi): void {
        $this->hobi = trim($hobi);
    }

    public function setAgama(string $agama): void {
        $val = trim($agama);
        if (!in_array($val, self::AGAMA_DIZINKAN, true)) {
            $pilihan = implode(', ', self::AGAMA_DIZINKAN);
            throw new InvalidArgumentException("Pilihan agama '$val' tidak valid! Pilihan yang diizinkan: $pilihan.");
        }
        $this->agama = $val;
    }
}
