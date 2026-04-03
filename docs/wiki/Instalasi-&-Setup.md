# ⚙️ Panduan Instalasi & Setup

Halaman ini menjelaskan cara menyiapkan lingkungan pengembangan dan menjalankan aplikasi Sinergi PAS.

## Persyaratan Sistem
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: v2.x
- **Node.js & NPM**: Versi terbaru (rekomendasi v20+)
- **Database**: MySQL 8.0 atau MariaDB 10.4+
- **Web Server**: Apache / Nginx (atau `php artisan serve` untuk dev)

## Langkah Instalasi

### 1. Persiapan Repositori
Clone project dan masuk ke direktori utama:
```bash
git clone https://github.com/Arya-Dian/sinergi-pas.git
cd sinergi-pas
```

### 2. Instalasi Otomatis (Recommended)
Kami telah menyediakan script untuk mempercepat proses setup:
```bash
composer setup
```

### 3. Konfigurasi Manual (Jika Diperlukan)
Jika script otomatis gagal, jalankan perintah berikut secara berurutan:
```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

### 4. Konfigurasi Database
Buka file `.env` dan sesuaikan pengaturan database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sinergi_pas
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Migrasi & Seeding
Isi database dengan struktur tabel dan data awal:
```bash
php artisan migrate --seed
```

### 6. Menjalankan Server
Gunakan perintah pintas untuk menjalankan backend, frontend, dan queue secara bersamaan:
```bash
composer dev
```
Aplikasi akan tersedia di `http://localhost:8000`.
