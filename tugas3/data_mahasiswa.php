<?php
declare(strict_types=1);

require_once __DIR__ . '/mahasiswa.php';

/**
 * Class DataMahasiswa (Repository Pengelola Objek Mahasiswa dalam Session)
 * Modul Praktikum 05: Enkapsulasi
 */
class DataMahasiswa {
    private string $sessionKey = 'mhs_repository_data';

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
            $this->seedInitialData();
        }
    }

    /**
     * Mengisi data awal demonstrasi mahasiswa
     */
    public function seedInitialData(): void {
        try {
            $m1 = new Mahasiswa(
                '2530521070',
                'Sanziya Rivaldi',
                'Sains dan Teknologi',
                'Teknik Informatika',
                'SMKN 1 Sukalarang',
                4,
                'Coding & Artificial Intelligence',
                'Islam'
            );
            $m2 = new Mahasiswa(
                '2310511002',
                'Alya Putri Maharani',
                'Sains dan Teknologi',
                'Teknik Informatika',
                'SMAN 3 Bandung',
                3,
                'Desain UI/UX & Membaca',
                'Islam'
            );
            $m3 = new Mahasiswa(
                '2310511015',
                'David Kristanto',
                'Sains dan Teknologi',
                'Sistem Informasi',
                'SMA Kristen Sukabumi',
                5,
                'Basket & Musik',
                'Kristen'
            );
            $this->tambah($m1);
            $this->tambah($m2);
            $this->tambah($m3);
        } catch (\Throwable) {
            // Seeder fallback
        }
    }

    /**
     * Mengambil seluruh data mahasiswa yang valid
     * @return array<string, Mahasiswa>
     */
    public function getAll(): array {
        if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
            return [];
        }

        $valid = [];
        foreach ($_SESSION[$this->sessionKey] as $key => $item) {
            if ($item instanceof Mahasiswa) {
                $valid[(string)$key] = $item;
            }
        }
        return $valid;
    }

    /**
     * Alias method untuk kompatibilitas
     * @return array<string, Mahasiswa>
     */
    public function semua(): array {
        return $this->getAll();
    }

    /**
     * Mencari mahasiswa berdasarkan NIM
     */
    public function cari(string $nim): ?Mahasiswa {
        $nim = trim($nim);
        if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
            return null;
        }

        $item = $_SESSION[$this->sessionKey][$nim] ?? null;
        return ($item instanceof Mahasiswa) ? $item : null;
    }

    /**
     * Menambahkan mahasiswa baru dengan validasi keunikan NIM
     * @throws \InvalidArgumentException Jika NIM sudah terdaftar
     */
    public function tambah(Mahasiswa $mhs): void {
        $nim = $mhs->getNim();
        if ($this->cari($nim) !== null) {
            throw new \InvalidArgumentException("NIM '{$nim}' sudah terdaftar dalam sistem! NIM harus unik.");
        }
        $_SESSION[$this->sessionKey][$nim] = $mhs;
    }

    /**
     * Memperbarui data objek mahasiswa yang sudah terdaftar
     * @throws \InvalidArgumentException Jika NIM tidak ditemukan
     */
    public function ubah(Mahasiswa $mhs): void {
        $nim = $mhs->getNim();
        if ($this->cari($nim) === null) {
            throw new \InvalidArgumentException("Gagal mengubah: Data mahasiswa dengan NIM '{$nim}' tidak ditemukan.");
        }
        $_SESSION[$this->sessionKey][$nim] = $mhs;
    }

    /**
     * Menghapus record mahasiswa berdasarkan NIM
     */
    public function hapus(string $nim): bool {
        $nim = trim($nim);
        if (isset($_SESSION[$this->sessionKey][$nim])) {
            unset($_SESSION[$this->sessionKey][$nim]);
            return true;
        }
        return false;
    }

    /**
     * Reset data ke kondisi sampel awal
     */
    public function resetData(): void {
        $_SESSION[$this->sessionKey] = [];
        $this->seedInitialData();
    }
}