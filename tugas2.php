<?php

class Buku {
    private string $idBuku;
    private string $judulBuku;
    private int $jumlahStok;

    public function __construct(string $idBuku, string $judulBuku, int $stokAwal = 0) {
        $this->idBuku = trim($idBuku);
        $this->setJudul($judulBuku);
        
        if ($stokAwal < 0) {
            throw new InvalidArgumentException("Nilai stok awal tidak boleh di bawah 0.");
        }
        $this->jumlahStok = $stokAwal;
    }

    public function getKodeBuku(): string {
        return $this->idBuku;
    }

    public function getJudul(): string {
        return $this->judulBuku;
    }

    public function setJudul(string $judul): void {
        $valJudul = trim($judul);
        if (empty($valJudul)) {
            throw new InvalidArgumentException("Judul buku tidak boleh kosong.");
        }
        $this->judulBuku = $valJudul;
    }

    public function getStok(): int {
        return $this->jumlahStok;
    }

    public function tambahStok(int $qty): void {
        if ($qty <= 0) {
            throw new InvalidArgumentException("Penambahan stok minimal bernilai 1.");
        }
        $this->jumlahStok += $qty;
    }

    public function kurangiStok(int $qty): void {
        if ($qty <= 0) {
            throw new InvalidArgumentException("Pengurangan stok minimal bernilai 1.");
        }
        if (($this->jumlahStok - $qty) < 0) {
            throw new InvalidArgumentException("Transaksi gagal. Stok tersisa hanya: " . $this->jumlahStok);
        }
        $this->jumlahStok -= $qty;
    }
}

// -------------------------------------------------------------------------
// PENGUJIAN SKENARIO LOGIKA (VALID & INVALID)
// -------------------------------------------------------------------------

$logValid = [];
$logInvalid = [];

// 1. Inisialisasi Objek Valid
$bukuBaru = new Buku("BK-101", "Dasar-Dasar OOP PHP", 8);
$logValid[] = "Status Inisialisasi: " . $bukuBaru->getKodeBuku() . " | " . $bukuBaru->getJudul() . " (Stok Mula: " . $bukuBaru->getStok() . ")";

$bukuBaru->tambahStok(4);
$bukuBaru->kurangiStok(2);
$logValid[] = "Stok Akhir Transaksi Normal: " . $bukuBaru->getStok() . " unit";

// 2. Pengujian Invalid 1: Judul Kosong
try {
    $bukuTesting = new Buku("BK-102", "Temp", 5);
    $bukuTesting->setJudul("");
} catch (InvalidArgumentException $e) {
    $logInvalid[] = "Judul Kosong: " . $e->getMessage();
}

// 3. Pengujian Invalid 2: Pengurangan Melebihi Stok
try {
    $bukuBaru->kurangiStok(50);
} catch (InvalidArgumentException $e) {
    $logInvalid[] = "Stok Kurang: " . $e->getMessage();
}

// 4. Pengujian Invalid 3: Tambah Stok Negatif
try {
    $bukuBaru->tambahStok(-10);
} catch (InvalidArgumentException $e) {
    $logInvalid[] = "Jumlah Negatif: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas 2 - Getter, Setter, & Validasi</title>
    <style>
    body {
        background-color: #0e1012;
        color: #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 2rem 0;
    }

    .card {
        background-color: #17191c;
        border: 1px solid #2d3748;
        border-radius: 12px;
        padding: 2.5rem;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }

    h2 {
        margin-top: 0;
        font-size: 1.8rem;
        color: #ffffff;
        border-bottom: 1px solid #2d3748;
        padding-bottom: 0.75rem;
        letter-spacing: -0.5px;
    }

    .section-title {
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: bold;
    }

    .valid-title {
        color: #00e676;
    }

    .invalid-title {
        color: #ff5252;
    }

    .log-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .log-list li {
        padding: 0.75rem 1rem;
        background-color: #0e1012;
        border: 1px solid #242830;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .log-valid {
        border-left: 4px solid #00e676;
    }

    .log-invalid {
        border-left: 4px solid #ff5252;
    }

    .highlight {
        color: #00e676;
        font-weight: 600;
    }
    </style>
</head>

<body>

    <div class="card">
        <h2>Manajemen Stok Buku (Enkapsulasi)</h2>

        <div class="section-title valid-title">✓ Pengujian Input Valid</div>
        <ul class="log-list">
            <?php foreach ($logValid as $valid): ?>
            <li class="log-valid"><?= $valid ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="section-title invalid-title">✕ Pengujian Input Tidak Valid</div>
        <ul class="log-list">
            <?php foreach ($logInvalid as $invalid): ?>
            <li class="log-invalid"><?= $invalid ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

</body>

</html>