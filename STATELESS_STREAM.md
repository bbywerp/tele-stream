# tele-stream sebagai penampil tanpa database

`tele-stream` hanya menyediakan halaman awal dan `/watch/{uuid}`. Saat halaman video dibuka, server meminta data ke `tele-autoposts` melalui `GET /api/stream/videos/{uuid}`. Data hanya berada di memori selama request tersebut dan tidak disalin ke database, file konten, session, atau cache persisten.

Konfigurasi server di `.env`:

```dotenv
AUTOPOST_API_URL=http://127.0.0.1:8000
AUTOPOST_API_TOKEN=
CACHE_DRIVER=array
SESSION_DRIVER=array
```

API video diakses menggunakan header `Authorization: Bearer <token>` oleh server tele-stream. `AUTOPOST_API_TOKEN` harus sama dengan `TELE_STREAM_API_TOKEN` di tele-autoposts; token tidak dikirim ke browser. Pada deployment lain, isi `AUTOPOST_API_URL` dengan alamat tele-autoposts tanpa akhiran `/api`. Setelah mengubah environment, bersihkan cache konfigurasi Laravel bila sebelumnya diaktifkan.

Database, migration, seeder, queue worker, login, dan dashboard admin tidak diperlukan di tele-stream. Rute CRUD, autentikasi, dan API katalog lokal sudah dinonaktifkan. Kode serta database lama tidak dihapus atau diakses oleh halaman streaming; pengujian CRUD/autentikasi lama diarsipkan di `tests/Legacy`, di luar suite aktif.

Data video dan metadata Bunny tetap dikelola di tele-autoposts. Series harus aktif dan video harus memiliki GUID Bunny yang valid. Pemutar Bunny ditandatangani pada server sumber jika Stream token key tersedia. Gunakan `stream_token_key` pada Bunny Setting; alias `token_key` lama tetap didukung.

Jika sumber melaporkan video tidak tersedia, halaman mengembalikan 404. Jika API sumber bermasalah atau konfigurasi tidak lengkap, halaman mengembalikan 503 tanpa menampilkan kredensial. Response HTML video menggunakan `Cache-Control: private, no-store` dan tidak membuat cookie sesi.

Token iframe mengikuti [dokumentasi resmi Bunny](https://github.com/BunnyWay/documentation/blob/main/stream/token-authentication.mdx); kebijakan referrer iframe mengikuti [panduan embed Bunny](https://github.com/BunnyWay/documentation/blob/main/stream/embedding.mdx). Video diputar langsung oleh Bunny; tele-stream tidak mengunduh atau menyimpan file videonya.

File aset statis dan kompilasi template Laravel tetap merupakan berkas aplikasi biasa. Tidak ada penyimpanan data katalog/pengguna di tele-stream.
