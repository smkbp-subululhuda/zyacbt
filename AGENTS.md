# Repository Guidelines

## Struktur Proyek

- `index.php` adalah front controller aplikasi CodeIgniter 3.1.11.
- `application/` berisi kode aplikasi: controller, model, view, config, helper, library, dan bahasa.
- `system/` berisi framework CodeIgniter; ubah hanya ketika ada alasan kompatibilitas yang jelas.
- `public/` menyimpan aset statis seperti Bootstrap, plugin, JavaScript, gambar, dan upload aplikasi.
- `*.sql` di root adalah dump database untuk instalasi atau pemulihan; `manual/` dan `Petunjuk update/` berisi dokumentasi pengguna.

## Build, Instalasi, dan Pengembangan

Cara utama menjalankan aplikasi adalah Docker Compose. Dependency PHPWord dan PhpSpreadsheet didefinisikan di `application/composer.json`.

```bash
docker compose up --build
```

Aplikasi tersedia di `http://localhost:8080`. Service database memakai MariaDB dan menyimpan data di `./database`; dump `zyacbt-public-2024-05-05-tanpa-database.sql` diimpor otomatis saat database pertama kali dibuat. Image aplikasi menggunakan PHP 8.4. Hentikan service dengan `docker compose down`. Untuk menjalankan tanpa Docker, gunakan PHP 8.4, MySQL, dan Composer dari root repositori serta sesuaikan `application/config/database.php` dan `application/config/config.php`.

## Gaya Kode dan Penamaan

Pertahankan gaya CodeIgniter yang sudah ada: indentasi tab pada PHP, kurung kurawal bergaya proyek, controller dengan nama PascalCase (contoh `Tes_dashboard.php`), dan method mengikuti konvensi CodeIgniter yang sudah digunakan. Gunakan nama deskriptif untuk controller, model, view, dan route; hindari perubahan format massal. Tidak ada formatter atau linter yang dikonfigurasi, jadi periksa diff sebelum commit.

## Pengujian

Repositori ini belum memiliki test suite otomatis atau persyaratan coverage. Minimal lakukan pemeriksaan sintaks pada file PHP yang diubah, misalnya `php -l application/controllers/Tes.php`, kemudian lakukan smoke test melalui browser untuk login, alur pengerjaan tes, dan fitur yang terdampak. Jika perubahan menyentuh database, uji pada database lokal menggunakan dump yang relevan.

## Commit dan Pull Request

Riwayat commit menggunakan subjek singkat berbahasa Indonesia, misalnya `Revisi database` atau `Menambah og-image`. Ikuti pola tersebut: gunakan kalimat imperatif singkat dan satu tujuan per commit. Pull request harus menjelaskan perubahan, konfigurasi atau langkah database yang diperlukan, hasil pengujian, serta screenshot untuk perubahan UI. Cantumkan issue terkait bila tersedia dan tandai risiko kompatibilitas PHP/MySQL.

## Keamanan dan Konfigurasi

Jangan commit kredensial database, konfigurasi produksi, atau data peserta. Perlakukan dump SQL dan isi `uploads/` sebagai data sensitif. Uji perubahan di environment development, dan pertahankan atribusi serta footer aplikasi sesuai ketentuan proyek.
