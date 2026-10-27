# Instruksi kerja

- Ikuti instruksi terbaru pengguna. Kerjakan hanya yang diminta, secara efisien dan tepat; jangan memperluas lingkup pekerjaan.
- Batasi pembacaan dan perubahan file pada workspace `C:\laragon\www\tele-autoposts` dan `C:\laragon\www\tele-stream`. File di luar workspace tetap di luar lingkup meskipun terbuka di IDE. Jangan mengubah konfigurasi Laragon, phpMyAdmin, file pribadi, atau proyek lain.
- Jika pekerjaan memerlukan akses di luar workspace, jelaskan kebutuhan tersebut dan tunggu instruksi pengguna. Jangan melakukannya otomatis.
- Hormati perubahan pengguna. Jangan menimpa perubahan yang tidak terkait, menghapus data, mengubah `.env`, menjalankan migrasi, atau menambah dependensi kecuali diperlukan dan termasuk dalam permintaan pengguna. Jangan tampilkan rahasia.
- Jangan melakukan commit, push, deploy, atau mengirim pesan ke layanan eksternal tanpa instruksi pengguna.

# Konteks proyek

- Katalog hasil impor berada di `tele-autoposts`, menggunakan `series` (UUID 10 karakter) dan `series_videos`. `tele-stream` hanya menampilkan data dari `GET /api/stream/videos/{uuid}` milik tele-autoposts, tanpa database, CRUD, login, session, atau cache konten persisten. Jangan menjalankan migration/seeder lokal otomatis. Panduan impor: `tele-autoposts/database/CATALOG_IMPORT.md`; panduan penampil: `tele-stream/STATELESS_STREAM.md`.

- `tele-stream` hanya menangani halaman publik dan `/watch/{uuid}`. Data hanya digunakan selama request; kode/model/migration CRUD lama tidak diaktifkan kembali. Komunikasi antarproyek melalui API, bukan query database lintas proyek.
- `tele-autoposts` menangani dashboard, Telegram, penjadwalan/job, affiliate, serta halaman show/redirect. Komunikasi antarproyek menggunakan REST API, bukan query langsung ke database proyek lain.
- Pertahankan arsitektur Laravel yang sudah ada dan gunakan dokumentasi proyek bila diperlukan.

# Data dan konfigurasi

- Proyek ini tidak memakai database. Semua katalog dan kredensial Bunny berada di tele-autoposts; jangan menyalin data video/pengguna ke tele-stream.
- Gunakan `AUTOPOST_API_URL` dan `AUTOPOST_API_TOKEN` pada environment. Token harus sama dengan `TELE_STREAM_API_TOKEN` di tele-autoposts dan dikirim sebagai Bearer token hanya antarpeladen, bukan ke browser. Token penandatanganan iframe Bunny tetap dikelola di tele-autoposts.
- Jangan membuat instance MySQL tambahan, mengganti port/kredensial/mesin database, atau mengubah konfigurasi phpMyAdmin tanpa permintaan pengguna.
- Folder `database/mysql/` merupakan cadangan tidak aktif dari pengaturan sebelumnya. Jangan mengaktifkan atau menghapusnya otomatis.

# Verifikasi dan pelaporan

- Periksa hanya hal yang relevan dengan perubahan. Perubahan dokumentasi saja tidak memerlukan test atau build aplikasi.
- Jangan mengirim pesan Telegram sungguhan dalam pengujian otomatis; gunakan mock HTTP.
- Laporkan perubahan dan pemeriksaan secara singkat dalam bahasa pengguna. Jangan mengklaim pemeriksaan yang belum dijalankan.
