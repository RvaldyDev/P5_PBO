# Laporan Tugas 4: Refleksi Konsep Enkapsulasi

## 1. Validasi Objek dan Visibilitas `private`

> **Pertanyaan:** Mengapa properti `private` saja belum cukup untuk memastikan objek selalu valid?

**Jawaban:**
Mengubah modifier properti menjadi `private` hanya berfungsi **mengisolasi dan mengunci akses langsung dari luar class** (_prevent direct external access_)[cite: 2, 3]. Hal tersebut belum menjamin keadaan state objek selalu valid karena:

- **Ketiadaan Validasi Internal:** Jika `__construct()` atau _setter_ internal menerima parameter tanpa mengecek aturan bisnis (misal: semester bernilai negatif atau nama bernilai string kosong), objek yang bernilai tidak valid tetap dapat dibuat dari dalam class.
- **Pengapsulan Aturan Bisnis (_Business Rules_):** Enkapsulasi bukan sekadar menyembunyikan data, melainkan mengontrol bagaimana state dapat diubah melalui aturan validasi yang konsisten di seluruh pintu masuk data.

## 2. Pengecualian Pembuatan _Setter_

> **Pertanyaan:** Kapan _setter_ tidak perlu dibuat? Berikan contoh dari class Mahasiswa atau Rekening.

**Jawaban:**
_Setter_ tidak perlu dibuat pada kondisi berikut:

1. **Identitas Tetap / _Read-Only Property_:**
   - _Contoh:_ Properti $nim pada class Mahasiswa. NIM diisi sekali saat diinstansiasi di \_construct(). Mengubah NIM di pertengahan jalan dapat merusak konsistensi indeks repository array/database.
2. **State Berbasis Transaksi Terstruktur:**
   - _Contoh:_ Properti $saldo pada class Rekening Tidak boleh ada setSaldo($nilai) bebas karena perubahan saldo wajib melalui operasi transaksi khusus yang memiliki validasi teratur, seperti setor($jumlah) dan tarik($jumlah).

---

## 3. Keamanan Method Berbasis Perilaku (_Behavior-Based_)

> **Pertanyaan:** Mengapa method tambahStok() / kurangiStok() lebih aman daripada setStok() bebas?

**Jawaban:**

- **setStok($nilai) (Kurang Aman):** Membolehkan pihak luar menimpa nilai stok mentah secara acak tanpa memperhatikan stok saat ini, yang berisiko melewati pengecekan batas bawah/negatif
- **`tambahStok()` & `kurangiStok()` (Lebih Aman):** Mengapsulkan logika operasi domain[cite: 5]. `tambahStok()` memastikan penambahan bernilai positif (`$jumlah > 0`), sedangkan `kurangiStok()` memvalidasi apakah sisa stok mencukupi sebelum pengurangan terjadi agar stok tidak bernilai negatif

---

## 4. Penggunaan Magic Methods vs Getter/Setter Eksplisit

> **Pertanyaan:** Apakah `__get()` dan `__set()` dari Pertemuan 4 wajib digunakan untuk menerapkan enkapsulasi? Jelaskan.[cite: 8]

**Jawaban:**
**Tidak wajib**, dan pada standar PHP 8+ modern justru lebih disarankan menggunakan _Getter_ dan _Setter_ eksplisit

- \*\*Kelemahan Magic Methods (_get / \_set): Menghilangkan \_strict type checking_ bawaan PHP 8+, menyulitkan pelacakan alur data, serta merusak fitur _autocomplete_ dan _static analysis_ pada IDE (seperti Intelephense di VS Code).
- **Keunggulan Method Eksplisit (`getNama()`, `setNama()`):** Menjaga type safety (_return type & parameter type hint_), mempermudah pembuatan validasi unik tiap atribut, serta membuat kontrak antarmuka class terlihat jelas[cite: 3, 4, 6].

---

## 5. Dampak Refactoring Akses Properti

> **Pertanyaan:** Bagaimana perubahan public menjadi private memengaruhi DataMahasiswa.php dan index.php?

**Jawaban:**

1. **Pada index.php (Tampilan):**
   - Akses langsung seperti `$m->nama` atau `$m->semester` akan memicu **Fatal Error (Error akses private)**
   - Semua pemanggilan wajib direfaktor menggunakan getter: `<?= aman($m->getNama()) ?>` dan `<?= aman($m->getSemester()) ?>.
2. **Pada DataMahasiswa.php (Repository):**
   - Penyiapan array key yang sebelumnya menggunakan `$m->nim` harus diubah menjadi `$this->data[$m->getNim()] = $m;.
3. **Peningkatan Keamanan:** Data internal objek Mahasiswa terproteksi penuh dari modifikasi ilegal secara tak sengaja dari luar class.
