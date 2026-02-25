<div align="center">

# 🏥 GUDANG-LAB
**Sistem Informasi Manajemen Inventaris & Stok Laboratorium**

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3.17-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

<p align="center">
  <b>Digitalisasi Operasional Gudang Laboratorium:</b><br>
  Dari Pengadaan Barang, Permintaan Ruangan, hingga Stock Opname — <i>Akurat & Terintegrasi.</i>
</p>

</div>

---

## 📖 Tentang Aplikasi

**GUDANG-LAB** adalah sistem informasi berbasis web yang dirancang khusus untuk digitalisasi inventaris barang di Unit Pelaksana Teknis Daerah (UPTD) Laboratorium Kesehatan. Sistem ini menggantikan pencatatan buku besar manual menjadi terkomputerisasi, sehingga memudahkan pelacakan reagen kimia, alat kesehatan (alkes), dan Barang Habis Pakai (BHP), serta memastikan transparansi alur distribusi barang antar ruangan.

---

## 🌟 Fitur Unggulan

| Modul | Deskripsi & Fungsionalitas |
| :--- | :--- |
| **📦 Master Data** | • **Katalog Barang:** Kelola kategori, satuan, dan data master barang (`ItemMaster`).<br>• **Varian & Batch:** Lacak *Expired Date* (ED) dan nomor *batch* barang (`ItemVariant`).<br>• **Manajemen Supplier:** Database pemasok barang laboratorium. |
| **🔄 Sirkulasi Stok & FEFO** | • **Metode FEFO (First Expired, First Out):** Sistem cerdas yang otomatis memprioritaskan pengeluaran barang dengan masa kedaluwarsa paling dekat untuk mencegah *dead stock* / barang rusak.<br>• **Barang Masuk & Keluar:** Pencatatan distribusi item dari gudang utama ke ruangan (`TransactionController`).<br>• **Stock Current:** Pantau ketersediaan stok secara *real-time*.<br>• **Stock Opname:** Penyesuaian fisik stok di gudang dengan data pada sistem. |
| **📝 Sistem Permintaan** | • **Request Ruangan:** User (ruangan) dapat mengajukan permintaan barang (`RequestController`).<br>• **Approval Berjenjang:** Validasi permintaan oleh pihak berwenang (`RequestApprovalController`). |
| **📊 Pelaporan (PDF)** | • **Kartu Stok:** Riwayat pergerakan (masuk/keluar) per item.<br>• **Laporan Komprehensif:** *Generate* PDF laporan barang keluar, distribusi, persetujuan, dan rekap penggunaan tahunan (`ReportController`). |

---

## 🛠 Teknologi

Project ini dibangun menggunakan fondasi teknologi modern yang stabil dan aman:

* **Backend Core:** `Laravel 10.x` (PHP 8.3.17)
* **Database:** `MySQL / MariaDB`
* **Frontend Asset:** `Vite`, `Blade Engine`
* **Styling & UI:** HTML5, CSS3, JavaScript
* **Auth & Security:** Autentikasi dan *Role-Based Policies* (`ItemMasterPolicy`, `RequestPolicy`, dll).

---

## 🚀 Panduan Instalasi Cepat

Ikuti langkah-langkah berikut untuk menjalankan project di *local environment* Anda:

### 1. Persiapan Awal
Pastikan komputer Anda sudah terinstall: `PHP >= 8.3`, `Composer`, dan `Node.js`.

### 2. Clone & Install Dependencies
Salin repository dan install library yang dibutuhkan:

```bash
# Clone repository
git clone [https://github.com/GilangAnnugroho/Gudang-Lab.git](https://github.com/GilangAnnugroho/Gudang-Lab.git)

# Masuk ke direktori project
cd Gudang-Lab

# Install Backend Dependencies
composer install

# Install Frontend Dependencies
npm install

```

### 3. Konfigurasi Environment

Duplikat file konfigurasi dan generate kunci aplikasi:

```bash
# Duplikat file env
cp .env.example .env

# Generate App Key
php artisan key:generate

```

### 4. Setup Database

Buat database kosong di phpMyAdmin (misal: `gudang_lab_db`). Buka file `.env` di text editor Anda, lalu sesuaikan bagian konfigurasi database:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gudang_lab_db 
DB_USERNAME=root
DB_PASSWORD=

```

### 5. Migrasi & Menjalankan Server

Jalankan perintah berikut untuk membuat tabel, memasukkan data *seeder* awal, dan memulai aplikasi:

```bash
# Migrasi tabel & Seeder (Untuk Data Master dan Akun)
php artisan migrate:fresh --seed

# Jalankan server Laravel (Terminal 1)
php artisan serve

# Compile aset frontend (Terminal 2)
npm run dev

```

🚀 **Aplikasi siap diakses di:** `http://127.0.0.1:8000`

---

## 📂 Struktur Direktori Utama

Berikut adalah peta struktur modul untuk memudahkan navigasi kode sistem Gudang-Lab:

```text
Gudang-Lab/
├── app/
│   ├── Console/Commands/         # 💻 Custom Commands (ex: ImportItemsCsv)
│   ├── Http/
│   │   ├── Controllers/          # 🧠 Logika Bisnis Utama (ItemMaster, Request, StockOpname, dll)
│   │   └── Requests/             # 🛡️ Validasi Form (StoreCategoryRequest, dll)
│   ├── Models/                   # 📦 Representasi Tabel Database (ItemVariant, Transaction, dll)
│   └── Policies/                 # 🔐 Aturan Hak Akses (RequestPolicy, TransactionPolicy)
├── database/
│   ├── migrations/               # ⚙️ Skema Tabel Database
│   └── seeders/                  # 🌱 Data Awal (StockCurrentSeeder, TransactionSeeder)
├── resources/
│   └── views/
│       ├── auth/                 # 🔑 Tampilan Login
│       ├── dashboard/            # 📊 Tampilan Utama Admin
│       ├── items/ & variants/    # 📝 Manajemen Master Data Barang
│       ├── layouts/              # 🎨 Template Utama (Sidebar, Topbar, Flash)
│       ├── reports/              # 🖨️ Template Laporan & PDF (Stock, Usage Yearly, dll)
│       ├── requests/             # 📝 Form Pengajuan Permintaan
│       ├── stock_opnames/        # 🔍 Fitur Penyesuaian Stok Gudang
│       └── transactions/         # 🔄 Fitur Barang Masuk & Keluar
└── routes/
    └── web.php                   # 🔗 Definisi URL & Routing

```

---

## 👤 Akses Demo (Seeder)

Sistem ini menggunakan struktur hak akses (*Role*). Gunakan akun bawaan berikut (hasil *seeder*) untuk pengujian sistem:

| Role / Akses | Email | Password | Fungsionalitas Utama |
| --- | --- | --- | --- |
| **Super Admin** | `admin@labkesda.com` | `password` | Kendali Penuh (Data Master, User, dsb). |
| **Kepala Lab** | `kepala@labkesda.com` | `password` | Memberikan *Approval* permintaan barang. |
| **Admin Gudang** | `gudang@labkesda.com` | `password` | Input barang masuk, distribusi barang keluar. |
| **Petugas Unit** | `namaunit@labkesda.com` | `password` | Membuat *Request* barang ke gudang. |

*(Catatan: Sesuaikan email di atas jika data seeder di dalam kode menggunakan email yang berbeda).*

---

## 🤝 Kontribusi

Sistem ini dikembangkan sebagai bagian dari tugas Praktik Kerja Lapangan (PKL) di UPTD Laboratorium Kesehatan. Jika ingin berkontribusi:

1. **Fork** repository ini.
2. Buat branch fitur baru: `git checkout -b fitur-baru`.
3. Commit perubahan: `git commit -m 'Menambahkan fitur XYZ'`.
4. Push ke branch: `git push origin fitur-baru`.
5. Submit **Pull Request**.

---

<div align="center">

**GUDANG-LAB** © 2026 • Dikembangkan oleh **[Gilang Annugroho](https://www.google.com/search?q=https://github.com/GilangAnnugroho)**.

</div>
