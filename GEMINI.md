# Proyek: Sinergi PAS - Sistem Informasi Kepegawaian Lapas Jombang

Sistem manajemen database kepegawaian internal untuk Lapas Jombang yang mencakup data pegawai, slip gaji, SKP, dan dokumen pribadi lainnya dengan fokus pada keamanan data, kemudahan akses (Self-Service), dan UI/UX yang modern serta premium.

## Arsitektur & Teknologi
- **Framework:** Laravel 11 (Latest Stable)
- **Frontend:** Blade Templating + Tailwind CSS
- **Database:** MySQL
- **UI Components:** Lucide Icons / FontAwesome, SweetAlert2, AOS (Animate On Scroll) atau Framer Motion (jika integrasi JS memungkinkan).
- **Fitur Utama:**
    - Role-Based Access Control (Superadmin vs Pegawai).
    - Manajemen Dokumen (Slip Gaji, SKP, Dokumen Kepegawaian).
    - Ekspor/Impor Multiformat (PDF, Excel, CSV, Word).
    - Dashboard Modern dengan Sidebar.

## Prinsip Desain (UI/UX)
- **Tema:** Light Mode (Primary).
- **Style:** Modern, clean, "Premium feel", tidak kaku.
- **Interaksi:** Hover effects yang halus, transisi antar halaman yang elegan, dan penggunaan icon yang representatif.
- **Layout:** Sidebar navigasi yang responsif, kartu (cards) untuk ringkasan data, dan tabel data yang rapi.

## Struktur Database (Garis Besar)
- `users`: Autentikasi dan data dasar.
- `employees`: Detail profil pegawai (NIP, Pangkat, Jabatan, dll).
- `documents`: Kategori (Slip Gaji, SKP, SK) dan file path.
- `categories`: Pengelompokan data pegawai.

## Panduan Pengembangan
- Gunakan **Migrations** untuk setiap perubahan skema database.
- Pastikan **Eloquent Relationships** terdefinisi dengan baik (One-to-Many untuk Dokumen).
- Gunakan **Service Classes** untuk logika bisnis yang kompleks (seperti generate PDF/Excel).
- Validasi input yang ketat terutama untuk upload dokumen.
