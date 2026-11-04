<?php

class Produk {
    public string $namaProduk;
    protected string $jenis;
    private int $hargaBeli;

    public function __construct(string $namaProduk, string $jenis, int $hargaBeli) {
        $this->namaProduk = $namaProduk;
        $this->jenis = $jenis;
        $this->hargaBeli = $hargaBeli;
    }
}

class ItemDigital extends Produk {
    public function ambilJenis(): string {
        return "Jenis produk ini: " . $this->jenis;
    }
}

$item1 = new ItemDigital("Modul Pembelajaran PHP", "E-Book", 45000);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Access Modifier</title>
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
    }

    .card {
        background-color: #17191c;
        border: 1px solid #2d3748;
        border-radius: 12px;
        padding: 2.5rem;
        width: 100%;
        max-width: 500px;
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

    .badge {
        display: inline-block;
        background-color: #00e676;
        color: #0e1012;
        font-weight: bold;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 4px;
        margin-bottom: 1rem;
        text-transform: uppercase;
    }

    .item-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .item-list li {
        padding: 0.85rem 0;
        border-bottom: 1px solid #242830;
        font-size: 1rem;
    }

    .item-list li:last-child {
        border-bottom: none;
    }

    .highlight {
        color: #00e676;
        font-weight: 600;
    }

    .label {
        color: #a0aec0;
    }
    </style>
</head>

<body>

    <div class="card">
        <span class="badge">Success</span>
        <h2>Uji Akses Access Modifier</h2>

        <ul class="item-list">
            <li>
                <span class="label">Public Access:</span> <br>
                <span class="highlight"><?= $item1->namaProduk ?></span>
            </li>
            <li>
                <span class="label">Protected via Subclass:</span> <br>
                <span><?= $item1->ambilJenis() ?></span>
            </li>
        </ul>
    </div>

</body>

</html>