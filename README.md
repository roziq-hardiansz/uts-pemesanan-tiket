# Sistem Pemesanan Tiket Acara Kampus

Aplikasi Laravel untuk reservasi tiket dengan autentikasi Laravel Breeze, pembatasan akses peserta/admin, transaksi database, dan pengiriman bukti melalui queue.

## Menjalankan aplikasi

Persyaratan: PHP 8.1+, Composer, Node.js/npm, dan database yang didukung Laravel.

1. Salin `.env.example` menjadi `.env`, lalu isi kredensial database dan SMTP.
2. Pasang dependensi dan siapkan aplikasi:

   ```sh
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

3. Pastikan `.env` memakai `QUEUE_CONNECTION=database`. Jalankan worker di terminal tersendiri:

   ```sh
   php artisan queue:work
   ```

4. Jalankan web server:

   ```sh
   php artisan serve --host=0.0.0.0 --port=8000
   ```

Daftar melalui halaman pendaftaran untuk membuat akun peserta. Setelah membuat akun admin secara terpisah, berikan peran administrator melalui Tinker:

```sh
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@kampus.ac.id')->firstOrFail();
$user->forceFill(['is_admin' => true])->save();
```

Pendaftaran publik tidak menerima atau menyimpan atribut `is_admin`. Endpoint `/admin/dashboard` dilindungi middleware `auth` dan `admin`.

## Memperlihatkan aplikasi dari luar jaringan

Untuk demo sementara, biarkan Laravel berjalan di port 8000, lalu jalankan salah satu tunnel di terminal lain:

```sh
cloudflared tunnel --url http://127.0.0.1:8000
```

atau:

```sh
ngrok http 8000
```

Gunakan URL HTTPS yang ditampilkan tunnel untuk demo. Sesuaikan `APP_URL` dengan URL tersebut dan jalankan `php artisan config:clear` bila URL dipakai aplikasi. Tunnel hanya meneruskan koneksi ke server lokal; tunnel tidak menambah kapasitas PHP, database, ataupun queue worker. Matikan tunnel setelah demo dan jangan publikasikan `.env`, kredensial, data peserta, atau aplikasi dengan `APP_DEBUG=true`.

## Batas transaksi dan queue

- Proses sinkron yang menentukan keberhasilan adalah validasi acara, penguncian baris acara, pemeriksaan kuota, pengurangan kuota, serta pencatatan booking. Semua perubahan database ini berada di satu transaksi.
- Constraint unik `user_id`/`event_id` mencegah satu akun memesan acara yang sama dua kali, sedangkan penguncian baris acara mencegah kuota terjual melebihi stok pada database yang mendukung row-level lock.
- Email bukti adalah pekerjaan tambahan. Job baru dikirim setelah transaksi booking commit, sehingga kegagalan SMTP tidak membatalkan reservasi yang sudah berhasil.
- Worker memeriksa `confirmation_sent_at` di bawah row lock sebelum mengirim email. Job yang dijalankan ulang setelah berhasil tidak mengirim ulang konfirmasi. Email eksternal dan commit database tidak dapat menjadi satu transaksi atomik; kegagalan proses tepat setelah SMTP menerima email tetapi sebelum penanda tersimpan masih dapat menyebabkan pengiriman ulang.

## Simulasi dan pengujian

Jalankan tes menggunakan database SQLite in-memory yang telah dikonfigurasi di `phpunit.xml`:

```sh
php artisan test
```

Untuk simulasi antrean lokal, buat booking lalu jalankan worker. Perintah berikut membantu memeriksa antrean dan job gagal:

```sh
php artisan queue:work --tries=3
php artisan queue:failed
```

## Keterbatasan saat akses bersamaan

- Semua pemesanan untuk acara yang sama mengunci baris acara yang sama. Ini menjaga kuota, tetapi permintaan tinggi dapat menunggu lock dan membatasi throughput.
- `lockForUpdate` efektif pada database relasional seperti MySQL/PostgreSQL dengan transaksi; SQLite tidak mewakili perilaku konkurensi produksi yang sama.
- Queue database dan satu worker memiliki kapasitas terbatas. Email lambat, koneksi database, pool worker, serta layanan SMTP dapat menjadi bottleneck. Tambahkan worker/queue backend dan pantau waktu tunggu serta job gagal untuk deployment yang lebih besar.
- Tunnel untuk pengujian bukan deployment produksi dan tidak memberi perlindungan, redundansi, atau peningkatan kapasitas server.
