# Toko Online Jajanan Tradisional 🇮🇩

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  Sebuah aplikasi E-commerce fungsional yang dibangun dengan <strong>Laravel 10</strong> untuk menjual aneka jajanan tradisional khas Indonesia.
</p>

<p align-center">
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework?style=for-the-badge" alt="Latest Stable Version"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge" alt="License"></a>
</p>

---

## 🌟 Tentang Proyek

**Toko Online Jajanan Tradisional** adalah sebuah platform web e-commerce yang dirancang untuk memudahkan penjualan dan pembelian makanan tradisional. Proyek ini memiliki dua sisi utama: halaman depan (frontend) yang elegan untuk pelanggan dan panel admin (backend) yang kuat untuk manajemen toko.

Aplikasi ini mengintegrasikan berbagai layanan pihak ketiga modern seperti **Midtrans** untuk gerbang pembayaran dan **RajaOngkir** untuk kalkulasi biaya pengiriman otomatis, memberikan pengalaman berbelanja yang lengkap dan mulus.

## ✨ Fitur Utama

### 🛍️ Frontend (Untuk Pelanggan)
-   **Autentikasi Pengguna**: Registrasi dan Login, termasuk login mudah dengan **Google OAuth**.
-   **Katalog Produk**: Jelajahi produk berdasarkan kategori seperti Brownies, Combro, Mochi, dll.
-   **Detail Produk**: Lihat informasi lengkap, harga, dan stok produk.
-   **Keranjang Belanja**: Tambah, lihat, dan kelola produk di keranjang.
-   **Kalkulasi Ongkir Otomatis**: Biaya pengiriman dihitung secara real-time menggunakan API **RajaOngkir**.
-   **Pembayaran Aman**: Proses checkout yang aman dengan berbagai metode pembayaran melalui **Midtrans**.
-   **Akun Pelanggan**: Kelola profil, alamat, dan lihat riwayat pesanan.

### ⚙️ Backend (Panel Admin)
-   **Dashboard Informatif**: Halaman utama yang menampilkan ringkasan aktivitas toko.
-   **Manajemen Pengguna**: CRUD (Create, Read, Update, Delete) untuk data admin.
-   **Manajemen Produk**: CRUD untuk produk, termasuk upload gambar dengan resize otomatis.
-   **Manajemen Kategori**: CRUD untuk kategori produk.
-   **Manajemen Pesanan**: Lihat dan kelola pesanan yang masuk dari pelanggan.
-   **Laporan**: Cetak laporan data pengguna dan produk dalam format PDF.
-   **Notifikasi Interaktif**: Menggunakan SweetAlert2 untuk konfirmasi aksi (seperti hapus data) yang lebih ramah pengguna.

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun menggunakan tumpukan teknologi modern dan andal:

-   **Framework**: [Laravel 10](https://laravel.com/)
-   **Bahasa**: PHP 8.1+
-   **Database**: MySQL
-   **Frontend**: Blade, Bootstrap, jQuery
-   **Backend**: Template [Matrix Admin](https://github.com/wrappixel/matrix-admin-bt4)
-   **API & Integrasi**:
    -   [Midtrans](https://midtrans.com/) (Payment Gateway)
    -   [RajaOngkir](https://rajaongkir.com/) (Shipping Cost API)
    -   Google OAuth (Socialite)
-   **Library Tambahan**:
    -   [SweetAlert2](https://sweetalert2.github.io/)
    -   [CKEditor](https://ckeditor.com/)

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat
-   PHP >= 8.1
-   Composer
-   Node.js & NPM (Opsional)
-   Database (MySQL/MariaDB)

### Langkah-langkah
1.  **Clone repository ini:**
    ```bash
    git clone https://github.com/username/TokoOnlineFInal.git
    cd TokoOnlineFInal
    ```

2.  **Install dependensi PHP:**
    ```bash
    composer install
    ```

3.  **Salin file environment:**
    ```bash
    cp .env.example .env
    ```

4.  **Generate kunci aplikasi:**
    ```bash
    php artisan key:generate
    ```

5.  **Konfigurasi file `.env`:**
    Buka file `.env` dan sesuaikan konfigurasi database dan kunci API:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=password_anda

    # Kunci API RajaOngkir (Starter/Basic)
    RAJAONGKIR_API_KEY=kunci_api_rajaongkir_anda

    # Kredensial Midtrans
    MIDTRANS_MERCHANT_ID=id_merchant_midtrans_anda
    MIDTRANS_CLIENT_KEY=client_key_midtrans_anda
    MIDTRANS_SERVER_KEY=server_key_midtrans_anda

    # Kredensial Google OAuth
    GOOGLE_CLIENT_ID=client_id_google_anda
    GOOGLE_CLIENT_SECRET=client_secret_google_anda
    GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
    ```

6.  **Jalankan migrasi dan seeder database:**
    Perintah ini akan membuat semua tabel dan mengisi data awal (admin, kategori, dll).
    ```bash
    php artisan migrate:refresh --seed
    ```

7.  **Buat symbolic link untuk storage:**
    Ini penting agar gambar yang di-upload dapat diakses dari web.
    ```bash
    php artisan storage:link
    ```

8.  **Jalankan server pengembangan:**
    ```bash
    php artisan serve
    ```
    Aplikasi sekarang dapat diakses di `http://localhost:8000`.

---

## 🔑 Akun Demo

Anda dapat menggunakan akun berikut yang dibuat oleh seeder untuk login:

-   **Admin:**
    -   **Email**: `admin@gmail.com`
    -   **Password**: `P@55word`
    -   **URL Login**: `http://localhost:8000/backend/login`

-   **Customer:**
    -   **Email**: `customer@gmail.com`
    -   **Password**: `customer123`

---


