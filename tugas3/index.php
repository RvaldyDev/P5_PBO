<?php
declare(strict_types=1);

/**
 * Aplikasi CRUD Mahasiswa Terenkapsulasi (Tugas 3)
 * Modul Praktikum 05: Enkapsulasi (Encapsulation)
 */

require_once __DIR__ . '/mahasiswa.php';
require_once __DIR__ . '/data_mahasiswa.php';

// Inisialisasi Repository
$repo = new DataMahasiswa();

// Ambil notifikasi dari session flash message (PRG Pattern)
$pesanSukses = $_SESSION['flash_sukses'] ?? '';
$pesanError  = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_sukses'], $_SESSION['flash_error']);

// State awal form
$editMhs  = null;
$formData = [
    'nim'         => '',
    'nama'        => '',
    'fakultas'    => 'Sains dan Teknologi',
    'prodi'       => '',
    'asalSekolah' => '',
    'semester'    => 1,
    'hobi'        => '',
    'agama'       => ''
];

// -----------------------------------------------------------------------------
// 1. Aksi Reset Data ke Sampel Awal
// -----------------------------------------------------------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] === 'reset') {
    $repo->resetData();
    $_SESSION['flash_sukses'] = "Data session berhasil direset ke data sampel demonstrasi!";
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// 2. Aksi Hapus Mahasiswa
// -----------------------------------------------------------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && !empty($_GET['nim'])) {
    $nimTarget = trim((string)$_GET['nim']);
    $mhs = $repo->cari($nimTarget);

    if ($mhs !== null) {
        $namaMhs = $mhs->getNama();
        $repo->hapus($nimTarget);
        $_SESSION['flash_sukses'] = "Data mahasiswa '{$namaMhs}' (NIM: {$nimTarget}) berhasil dihapus!";
    } else {
        $_SESSION['flash_error'] = "Data mahasiswa dengan NIM {$nimTarget} tidak ditemukan!";
    }

    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------------------
// 3. Aksi Persiapan Edit Mahasiswa
// -----------------------------------------------------------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] === 'edit' && !empty($_GET['nim'])) {
    $nimTarget = trim((string)$_GET['nim']);
    $editMhs   = $repo->cari($nimTarget);

    if ($editMhs !== null) {
        $formData = [
            'nim'         => $editMhs->getNim(),
            'nama'        => $editMhs->getNama(),
            'fakultas'    => $editMhs->getFakultas(),
            'prodi'       => $editMhs->getProdi(),
            'asalSekolah' => $editMhs->getAsalSekolah(),
            'semester'    => $editMhs->getSemester(),
            'hobi'        => $editMhs->getHobi(),
            'agama'       => $editMhs->getAgama()
        ];
    } else {
        $pesanError = "Data mahasiswa dengan NIM '{$nimTarget}' tidak ditemukan untuk diedit!";
    }
}

// -----------------------------------------------------------------------------
// 4. Proses Submit Form (Tambah / Update Mahasiswa)
// -----------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $isEdit      = isset($_POST['is_edit']) && $_POST['is_edit'] === '1';
    $nim         = trim((string)($_POST['nim'] ?? ''));
    $nama        = trim((string)($_POST['nama'] ?? ''));
    $fakultas    = trim((string)($_POST['fakultas'] ?? ''));
    $prodi       = trim((string)($_POST['prodi'] ?? ''));
    $asalSekolah = trim((string)($_POST['asalSekolah'] ?? ''));
    $semesterRaw = $_POST['semester'] ?? '';
    $hobi        = trim((string)($_POST['hobi'] ?? ''));
    $agama       = trim((string)($_POST['agama'] ?? ''));

    // Validasi format angka bulat semester
    $semesterValid = filter_var($semesterRaw, FILTER_VALIDATE_INT);
    $semesterInt   = ($semesterValid !== false) ? (int)$semesterValid : 0;

    // Menyimpan input pengguna agar tetap tampil di form jika validasi gagal
    $formData = [
        'nim'         => $nim,
        'nama'        => $nama,
        'fakultas'    => $fakultas,
        'prodi'       => $prodi,
        'asalSekolah' => $asalSekolah,
        'semester'    => ($semesterValid !== false) ? $semesterInt : (int)$semesterRaw,
        'hobi'        => $hobi,
        'agama'       => $agama
    ];

    try {
        if ($semesterValid === false) {
            throw new InvalidArgumentException("Semester harus berupa angka bulat yang valid antara 1 sampai 14!");
        }

        if ($isEdit) {
            $mhsExisting = $repo->cari($nim);
            if ($mhsExisting === null) {
                throw new InvalidArgumentException("Data mahasiswa yang akan diperbarui tidak ditemukan!");
            }

            // Memperbarui objek melalui setter yang diizinkan (Enkapsulasi State)
            $mhsExisting->setNama($nama);
            $mhsExisting->setFakultas($fakultas);
            $mhsExisting->setProdi($prodi);
            $mhsExisting->setAsalSekolah($asalSekolah);
            $mhsExisting->setSemester($semesterInt);
            $mhsExisting->setHobi($hobi);
            $mhsExisting->setAgama($agama);

            $repo->ubah($mhsExisting);

            $_SESSION['flash_sukses'] = "Data mahasiswa '{$mhsExisting->getNama()}' (NIM: {$nim}) berhasil diperbarui!";
            header('Location: index.php');
            exit;
        } else {
            $mhsBaru = new Mahasiswa($nim, $nama, $fakultas, $prodi, $asalSekolah, $semesterInt, $hobi, $agama);
            $repo->tambah($mhsBaru);

            $_SESSION['flash_sukses'] = "Mahasiswa baru '{$mhsBaru->getNama()}' (NIM: {$nim}) berhasil ditambahkan!";
            header('Location: index.php');
            exit;
        }
    } catch (InvalidArgumentException $e) {
        $pesanError = $e->getMessage();
        if ($isEdit) {
            $editMhs = $repo->cari($nim);
        }
    }
}

// Ambil seluruh data mahasiswa tersimpan di session
$daftarMahasiswa = $repo->getAll();

/**
 * Sanitasi string untuk output HTML aman (Mencegah XSS)
 */
function aman(mixed $str): string {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard CRUD Mahasiswa - Enkapsulasi PBO</title>
    <style>
    :root {
        --bg-color: #0b0d10;
        --panel-bg: #14171d;
        --panel-card: #181c24;
        --accent-cyan: #00f2fe;
        --accent-blue: #4facfe;
        --accent-green: #00e676;
        --accent-red: #ff5252;
        --text-main: #e2e8f0;
        --text-muted: #8a99ad;
        --border-color: #232936;
        --border-focus: #00f2fe;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-main);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 1.5rem;
        min-height: 100vh;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Navigasi Praktikum */
    .nav-praktikum {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 0.85rem 1.25rem;
        background-color: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .nav-brand {
        font-weight: 700;
        font-size: 0.95rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-brand span {
        color: var(--accent-cyan);
    }

    .nav-links {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .nav-link {
        padding: 0.4rem 0.85rem;
        border-radius: 6px;
        font-size: 0.825rem;
        text-decoration: none;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: #ffffff;
        border-color: var(--accent-blue);
        background-color: rgba(79, 172, 254, 0.1);
    }

    .nav-link.active {
        color: #0b0d10;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
        border-color: transparent;
        font-weight: 700;
    }

    /* Header Title */
    .header-title {
        text-align: center;
        margin-bottom: 2rem;
    }

    .header-title h1 {
        font-size: 2.2rem;
        margin: 0;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }

    .header-title p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-top: 0.4rem;
    }

    /* Alert Box */
    .alert {
        padding: 0.9rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: fadeIn 0.3s ease;
    }

    .alert-content {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .alert-error {
        background-color: rgba(255, 82, 82, 0.12);
        border: 1px solid var(--accent-red);
        color: var(--accent-red);
    }

    .alert-success {
        background-color: rgba(0, 230, 118, 0.12);
        border: 1px solid var(--accent-green);
        color: var(--accent-green);
    }

    /* Dashboard Grid Layout */
    .dashboard-container {
        display: grid;
        grid-template-columns: 390px 1fr;
        gap: 1.75rem;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .dashboard-container {
            grid-template-columns: 1fr;
        }
    }

    /* Panel Card */
    .panel {
        background-color: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
    }

    .panel-header {
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.85rem;
    }

    /* Form Styling */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .form-group label small {
        text-transform: none;
        font-weight: normal;
        color: var(--accent-cyan);
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.85rem;
        background-color: #0b0d10;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-main);
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--border-focus);
        box-shadow: 0 0 8px rgba(0, 242, 254, 0.25);
    }

    .form-control[readonly] {
        background-color: #1a1e27;
        color: var(--text-muted);
        cursor: not-allowed;
        border-color: #2a3140;
    }

    select.form-control {
        cursor: pointer;
    }

    select.form-control option {
        background-color: #14171d;
        color: #ffffff;
    }

    .btn-submit {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
        border: none;
        border-radius: 6px;
        color: #0b0d10;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        margin-top: 0.75rem;
        transition: opacity 0.2s, transform 0.1s;
    }

    .btn-submit:hover {
        opacity: 0.92;
    }

    .btn-submit:active {
        transform: scale(0.99);
    }

    .btn-cancel {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        color: var(--accent-red);
        background-color: rgba(255, 82, 82, 0.12);
        border: 1px solid rgba(255, 82, 82, 0.3);
        font-size: 0.8rem;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-cancel:hover {
        background-color: rgba(255, 82, 82, 0.22);
    }

    /* Search & Table Toolbar */
    .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        background-color: #0b0d10;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.55rem 0.85rem;
        color: var(--text-main);
        font-size: 0.85rem;
        width: 260px;
        max-width: 100%;
        transition: border-color 0.2s;
    }

    .search-box:focus {
        outline: none;
        border-color: var(--accent-cyan);
    }

    .badge-count {
        font-size: 0.8rem;
        color: var(--accent-cyan);
        background-color: rgba(0, 242, 254, 0.1);
        border: 1px solid rgba(0, 242, 254, 0.3);
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        font-weight: 600;
    }

    /* Table Styling */
    .table-responsive {
        overflow-x: auto;
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.88rem;
    }

    th {
        background-color: #0b0d10;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .sub-meta {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .nim-code {
        font-family: 'Consolas', 'Courier New', Courier, monospace;
        color: var(--accent-cyan);
        font-weight: 600;
        background-color: rgba(0, 242, 254, 0.08);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .badge-smt {
        background-color: rgba(79, 172, 254, 0.15);
        color: var(--accent-blue);
        border: 1px solid rgba(79, 172, 254, 0.3);
    }

    .badge-agama {
        background-color: rgba(255, 255, 255, 0.06);
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }

    .badge-hobi {
        background-color: rgba(0, 230, 118, 0.12);
        color: var(--accent-green);
        border: 1px solid rgba(0, 230, 118, 0.25);
    }

    .actions-cell {
        white-space: nowrap;
    }

    .btn-action {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        margin-right: 4px;
        transition: all 0.2s;
    }

    .btn-edit {
        background-color: rgba(79, 172, 254, 0.15);
        border: 1px solid rgba(79, 172, 254, 0.35);
        color: var(--accent-blue);
    }

    .btn-edit:hover {
        background-color: rgba(79, 172, 254, 0.3);
    }

    .btn-delete {
        background-color: rgba(255, 82, 82, 0.15);
        border: 1px solid rgba(255, 82, 82, 0.35);
        color: var(--accent-red);
    }

    .btn-delete:hover {
        background-color: rgba(255, 82, 82, 0.3);
    }

    .empty-placeholder {
        text-align: center;
        color: var(--text-muted);
        padding: 3rem 1rem;
    }

    .empty-placeholder p {
        margin: 0.5rem 0 0 0;
        font-size: 0.9rem;
    }

    /* Footer */
    .footer-info {
        text-align: center;
        color: var(--text-muted);
        font-size: 0.825rem;
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>

    <div class="container">

        <!-- Navigasi Praktikum P5 -->
        <div class="nav-praktikum">
            <a href="../index.php" class="nav-brand" style="text-decoration: none;">
                <span>⚡</span> PBO Praktikum 5: <strong>Enkapsulasi</strong>
            </a>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">🏠 Portal Utama</a>
                <a href="../tugas1.php" class="nav-link">Tugas 1 (Access Modifier)</a>
                <a href="../tugas2.php" class="nav-link">Tugas 2 (Getter & Setter)</a>
                <a href="index.php" class="nav-link active">Tugas 3 (CRUD Enkapsulasi)</a>
            </div>

            <!-- Judul Header -->
            <div class="header-title">
                <h1>Mahasiswa Data Center</h1>
                <p>Implementasi Enkapsulasi Strict State Integrity & Session Repository</p>
            </div>

            <!-- Alert Notifikasi -->
            <?php if (!empty($pesanError)): ?>
            <div class="alert alert-error">
                <div class="alert-content">
                    <strong style="font-size: 1.1rem;">✕</strong>
                    <span><?= aman($pesanError) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($pesanSukses)): ?>
            <div class="alert alert-success">
                <div class="alert-content">
                    <strong style="font-size: 1.1rem;">✓</strong>
                    <span><?= aman($pesanSukses) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Container Utama Dashboard -->
            <div class="dashboard-container">

                <!-- Panel Kiri: Form Input / Edit -->
                <div class="panel">
                    <div class="panel-header">
                        <span><?= $editMhs ? '✎ Edit Data Mahasiswa' : '+ Tambah Mahasiswa' ?></span>
                        <?php if ($editMhs): ?>
                        <a href="index.php" class="btn-cancel">Batal Edit</a>
                        <?php endif; ?>
                    </div>

                    <form action="index.php" method="POST">
                        <?php if ($editMhs): ?>
                        <input type="hidden" name="is_edit" value="1">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>
                                NIM (Nomor Induk Mahasiswa)
                                <?php if ($editMhs): ?>
                                <small>(Read-Only)</small>
                                <?php endif; ?>
                            </label>
                            <input type="text" name="nim" class="form-control" value="<?= aman($formData['nim']) ?>"
                                <?= $editMhs ? 'readonly' : 'required' ?> placeholder="Contoh: 2530521099"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= aman($formData['nama']) ?>"
                                required placeholder="Nama lengkap mahasiswa">
                        </div>

                        <div class="form-group">
                            <label>Fakultas</label>
                            <input type="text" name="fakultas" class="form-control"
                                value="<?= aman($formData['fakultas']) ?>" required
                                placeholder="Contoh: Sains dan Teknologi">
                        </div>

                        <div class="form-group">
                            <label>Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="<?= aman($formData['prodi']) ?>"
                                required placeholder="Contoh: Teknik Informatika">
                        </div>

                        <div class="form-group">
                            <label>Semester (1 - 14)</label>
                            <input type="number" name="semester" class="form-control"
                                value="<?= aman((string)$formData['semester']) ?>" min="1" max="14" required>
                        </div>

                        <div class="form-group">
                            <label>Asal Sekolah</label>
                            <input type="text" name="asalSekolah" class="form-control"
                                value="<?= aman($formData['asalSekolah']) ?>" placeholder="Contoh: SMAN 1 / SMKN 2">
                        </div>

                        <div class="form-group">
                            <label>Hobi</label>
                            <input type="text" name="hobi" class="form-control" value="<?= aman($formData['hobi']) ?>"
                                placeholder="Contoh: Coding, Game, Musik">
                        </div>

                        <div class="form-group">
                            <label>Agama</label>
                            <select name="agama" class="form-control" required>
                                <option value="">-- Pilih Agama --</option>
                                <?php foreach (Mahasiswa::AGAMA_DIZINKAN as $agm): ?>
                                <option value="<?= aman($agm) ?>"
                                    <?= ($formData['agama'] === $agm) ? 'selected' : '' ?>>
                                    <?= aman($agm) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit">
                            <?= $editMhs ? 'Simpan Perubahan' : '+ Tambah Data Mahasiswa' ?>
                        </button>
                    </form>
                </div>

                <!-- Panel Kanan: Tabel Data Mahasiswa -->
                <div class="panel">
                    <div class="panel-header">
                        <span>Daftar Record Mahasiswa</span>
                        <span class="badge-count">Total: <?= count($daftarMahasiswa) ?> Record</span>
                    </div>

                    <div class="table-toolbar">
                        <input type="text" id="searchInput" class="search-box"
                            placeholder="🔍 Cari nama, NIM, prodi, hobi..." autocomplete="off">
                        <a href="index.php?aksi=reset" class="btn-cancel"
                            style="color: var(--text-muted); border-color: var(--border-color); background: rgba(255, 255, 255, 0.05); text-decoration: none; padding: 0.5rem 0.85rem; font-size: 0.82rem;"
                            onclick="return confirm('Apakah Anda yakin ingin me-reset data ke kondisi sampel demonstrasi?')">↺
                            Reset Data Awal</a>
                    </div>

                    <div class="table-responsive">
                        <table id="mhsTable">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama &amp; Asal Sekolah</th>
                                    <th>Program Studi &amp; Fakultas</th>
                                    <th>Smt</th>
                                    <th>Agama</th>
                                    <th>Hobi</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftarMahasiswa)): ?>
                                <tr id="emptyRow">
                                    <td colspan="7" class="empty-placeholder">
                                        <div style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.6;">📂</div>
                                        <strong>Belum ada data mahasiswa tersimpan di session.</strong>
                                        <p>Silakan isi formulir di sebelah kiri untuk menambahkan data mahasiswa baru.
                                        </p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($daftarMahasiswa as $m): ?>
                                <tr>
                                    <td>
                                        <span class="nim-code"><?= aman($m->getNim()) ?></span>
                                    </td>
                                    <td>
                                        <strong style="color: #ffffff;"><?= aman($m->getNama()) ?></strong>
                                        <div class="sub-meta">Asal: <?= aman($m->getAsalSekolah() ?: '-') ?></div>
                                    </td>
                                    <td>
                                        <div><?= aman($m->getProdi()) ?></div>
                                        <div class="sub-meta"><?= aman($m->getFakultas()) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge badge-smt">Smt <?= aman((string)$m->getSemester()) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-agama"><?= aman($m->getAgama()) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-hobi"><?= aman($m->getHobi() ?: '-') ?></span>
                                    </td>
                                    <td class="actions-cell" style="text-align: center;">
                                        <a href="index.php?aksi=edit&nim=<?= urlencode($m->getNim()) ?>"
                                            class="btn-action btn-edit" title="Edit Mahasiswa">Edit</a>
                                        <a href="index.php?aksi=hapus&nim=<?= urlencode($m->getNim()) ?>"
                                            class="btn-action btn-delete"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data <?= aman(addslashes($m->getNama())) ?> (NIM: <?= aman($m->getNim()) ?>)?')"
                                            title="Hapus Mahasiswa">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="footer-info">
                Pemrograman Berorientasi Objek (PBO) &bull; Pertemuan 5: Enkapsulasi Strict State Integrity &bull; PHP
                <?= PHP_VERSION ?>
            </div>

        </div>

        <!-- JavaScript Filter Pencarian Realtime -->
        <script>
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#mhsTable tbody tr:not(#emptyRow)');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        });
        </script>
</body>

</html>