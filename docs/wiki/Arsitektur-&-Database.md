# 📂 Arsitektur & Database

Halaman ini ditujukan untuk pengembang yang ingin memodifikasi atau memperluas fitur Sinergi PAS.

## Skema Database Utama
- **`users`**: Tabel autentikasi utama (role: `superadmin`, `pegawai`).
- **`employees`**: Berisi detail profil (NIP, Pangkat, Unit Kerja). Terhubung 1:1 dengan `users`.
- **`documents`**: Menyimpan path file, status verifikasi (`pending`, `verified`, `rejected`), dan metadata tahun/bulan.
- **`document_categories`**: Master kategori (Slip Gaji, SKP, Ijazah). Memiliki flag `is_mandatory`.
- **`audit_logs`**: Log aktivitas sistem menggunakan event listener Laravel.

## Alur Bisnis (Logic Flow)
### Alur Verifikasi Dokumen
1. Pegawai mengunggah file (disimpan di `storage/app/private`).
2. Event `DocumentUploaded` dipicu -> Kirim notifikasi ke Admin.
3. Admin melakukan review.
4. Jika `Verify` -> Status berubah, dokumen dikunci (opsional).
5. Jika `Reject` -> Status berubah, alasan penolakan disimpan, notifikasi dikirim ke Pegawai.

## Teknologi Utama
- **Laravel 12 (Service Layer)**: Digunakan untuk validasi ketat dan manajemen file private.
- **Tailwind CSS 4 (UI)**: Styling modern dengan performa tinggi.
- **Vite 7 (Bundler)**: Asset bundling yang sangat cepat.
- **DomPDF**: Untuk pembuatan laporan dinamis.
- **Intervention Image**: Untuk kompresi foto profil agar hemat storage.

## Keamanan File
Semua dokumen disimpan di disk `private` yang tidak dapat diakses langsung melalui URL publik. Akses ke file dilindungi oleh middleware `auth` dan controller khusus yang memeriksa hak kepemilikan file.
