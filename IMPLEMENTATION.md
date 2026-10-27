# TeleStream

Aplikasi Laravel 10 untuk katalog series/video, REST API antaraplikasi, dan player streaming. Pasangan aplikasi ini berada di `../tele-autoposts` dan tidak mengakses database ini secara langsung.

Panduan lengkap dua aplikasi: [IMPLEMENTATION.md](../tele-autoposts/IMPLEMENTATION.md).

- URL lokal: `http://127.0.0.1:8001`, jalankan `php artisan serve --host=127.0.0.1 --port=8001`.
- Database MySQL Laragon lokal: `tele_stream` di `127.0.0.1:3306`, user `root`, tanpa password. Jalankan MySQL melalui Laragon seperti biasa; migrasi sudah tersedia.
- Buat admin manual melalui Tinker/database dengan `is_admin=true`; tidak ada kredensial default atau registrasi publik.
- Login `/login`, ringkasan `/dashboard`, katalog `/dashboard/series`, token `/dashboard/settings/api`.
- Set token acak minimal 32 karakter dan gunakan token yang sama di dashboard tele-autoposts.
- Minimal 3 video aktif diperlukan agar series aktif tersedia untuk pemilihan posting.
- Streaming publik `/watch/{uuid}`; MP4 HTML5 atau HLS `.m3u8`. URL eksternal harus mendukung CORS/codec yang sesuai.
- API: `/api/stats`, `/api/series`, `/api/series/random`, `/api/series/{uuid}`, `/api/series/{uuid}/videos`, `/api/videos/{uuid}`. Semua memerlukan Bearer token.
- Token terenkripsi; stream URL tidak tersedia di API pemilihan konten. UUID menjadi identitas publik.
- Migration baru: `2026_10_25_000001_add_admin_to_users.php`, `2026_10_25_000002_create_catalog_tables.php`.
- Tidak memerlukan worker/scheduler; keduanya berjalan di tele-autoposts.

Jalankan test memakai `php artisan test` dengan ekstensi pdo_sqlite aktif. Pada PHP CLI Laragon sesi ini:

```powershell
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/phpunit/phpunit/phpunit --no-progress
php vendor/bin/pint --test
npm run build
```

Hasil akhir ada pada [VERIFICATION.md](VERIFICATION.md). Telegram nyata, stream eksternal nyata, visual browser, dan operasi VPS belum diuji. Tidak dilakukan commit, push, atau deploy.
