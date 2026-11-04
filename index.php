<?php
/**
 * Portal Praktikum 5 - Pemrograman Berorientasi Objek (PBO)
 * Navigasi Tugas 1, Tugas 2, dan Tugas 3
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBO Praktikum 5 - Enkapsulasi &amp; Access Modifier</title>
    <style>
        :root {
            --bg-color: #0b0d10;
            --panel-bg: #14171d;
            --accent-cyan: #00f2fe;
            --accent-blue: #4facfe;
            --accent-green: #00e676;
            --text-main: #e2e8f0;
            --text-muted: #8a99ad;
            --border-color: #232936;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 2.5rem 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header-title {
            text-align: center;
            margin-bottom: 2.5rem;
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
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .task-card {
            background-color: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.75rem;
            text-decoration: none;
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            transition: all 0.25s ease;
        }

        .task-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-cyan);
            box-shadow: 0 14px 30px rgba(0, 242, 254, 0.15);
        }

        .task-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            margin-bottom: 1rem;
            width: fit-content;
        }

        .badge-1 {
            background-color: rgba(79, 172, 254, 0.15);
            color: var(--accent-blue);
            border: 1px solid rgba(79, 172, 254, 0.3);
        }

        .badge-2 {
            background-color: rgba(0, 230, 118, 0.15);
            color: var(--accent-green);
            border: 1px solid rgba(0, 230, 118, 0.3);
        }

        .badge-3 {
            background: linear-gradient(135deg, rgba(0, 242, 254, 0.2), rgba(79, 172, 254, 0.2));
            color: var(--accent-cyan);
            border: 1px solid rgba(0, 242, 254, 0.4);
        }

        .task-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: #ffffff;
        }

        .task-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .task-action {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent-cyan);
            gap: 0.4rem;
        }

        .footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-title">
            <h1>Praktikum 5: Pemrograman Berorientasi Objek</h1>
            <p>Implementasi Enkapsulasi, Access Modifier, &amp; Strict State Integrity</p>
        </div>

        <div class="grid-cards">
            <!-- Tugas 1 -->
            <a href="tugas1.php" class="task-card">
                <div>
                    <span class="task-badge badge-1">Tugas 1</span>
                    <h2 class="task-title">Access Modifier</h2>
                    <p class="task-desc">
                        Demonstrasi hak akses <code>public</code>, <code>protected</code>, dan <code>private</code> pada class Produk &amp; ItemDigital.
                    </p>
                </div>
                <div class="task-action">
                    Buka Tugas 1 &rarr;
                </div>
            </a>

            <!-- Tugas 2 -->
            <a href="tugas2.php" class="task-card">
                <div>
                    <span class="task-badge badge-2">Tugas 2</span>
                    <h2 class="task-title">Getter, Setter, &amp; Validasi</h2>
                    <p class="task-desc">
                        Pengujian transaksi stok buku dengan validasi ketat, exception handling, dan perlindungan state.
                    </p>
                </div>
                <div class="task-action">
                    Buka Tugas 2 &rarr;
                </div>
            </a>

            <!-- Tugas 3 -->
            <a href="tugas3/index.php" class="task-card">
                <div>
                    <span class="task-badge badge-3">Tugas 3 (Utama)</span>
                    <h2 class="task-title">CRUD Enkapsulasi Mahasiswa</h2>
                    <p class="task-desc">
                        Dashboard CRUD Mahasiswa lengkap berbasis enkapsulasi, validasi state integrity, dan session repository.
                    </p>
                </div>
                <div class="task-action">
                    Buka Dashboard Tugas 3 &rarr;
                </div>
            </a>
        </div>

        <div class="footer">
            PBO Praktikum 5 &bull; PHP <?= PHP_VERSION ?>
        </div>
    </div>

</body>

</html>
