# ♻️ Bank Rongsok

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-13-red?style=for-the-badge\&logo=laravel)
![Vue](https://img.shields.io/badge/Vue-3-42b883?style=for-the-badge\&logo=vue.js)
![TypeScript](https://img.shields.io/badge/TypeScript-3178C6?style=for-the-badge\&logo=typescript)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge\&logo=mysql)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum-blue?style=for-the-badge)

### 🌱 Platform Digital Pengelolaan Bank Sampah & Kegiatan Sosial

Aplikasi web modern untuk membantu pengelolaan penjualan rongsok, dana sosial, dokumentasi kegiatan, dan distribusi bantuan secara transparan dan terintegrasi.

</div>

---

# ✨ Fitur Utama

## 🔐 Authentication & Security

* Login & Register menggunakan Laravel Sanctum
* Verifikasi email menggunakan OTP Gmail
* Resend OTP
* Protected API Routes
* Token Authentication

## ♻️ Manajemen Penjualan Rongsok

* CRUD data penjualan rongsok
* Statistik total penjualan
* Monitoring pemasukan bulanan
* Dashboard analytics

## 📸 Galeri Kegiatan Sosial

* CRUD kegiatan sosial
* Upload foto kegiatan
* Konversi otomatis gambar ke format WebP
* Galeri dokumentasi kegiatan
* Detail kegiatan

## 💰 Pengelolaan Dana Sosial

* Monitoring dana terkumpul
* Target penggalangan dana
* Progress donasi
* Statistik dana bantuan

## ❤️ Penyaluran Bantuan

* Data distribusi bantuan
* Jumlah penerima bantuan
* Riwayat penyaluran
* Monitoring aktivitas sosial

## 📊 Dashboard Analytics

* Statistik realtime
* Grafik pendapatan bulanan menggunakan ApexCharts
* Data kegiatan terbaru
* Monitoring distribusi bantuan

---

# 🖼️ Preview Tampilan

## Dashboard Admin

* Statistik kegiatan
* Grafik penjualan rongsok
* Informasi dana sosial
* Monitoring distribusi bantuan

## Galeri Kegiatan

* Upload dokumentasi kegiatan
* Preview gambar
* Responsive gallery
* Optimasi gambar WebP

---

# 🛠️ Tech Stack

## Backend

* Laravel 13
* Laravel Sanctum
* MySQL
* REST API
* Intervention Image

## Frontend

* Vue 3
* TypeScript
* Vue Router
* Axios
* ApexCharts
* Bootstrap 5

---

# 📂 Struktur Project

```bash
bank-rongsok/
│
├── backend/
│   ├── app/
│   ├── routes/
│   ├── database/
│   └── resources/
│
├── frontend/
│   ├── src/
│   ├── components/
│   ├── views/
│   └── layouts/
│
└── README.md
```

---

# ⚙️ Instalasi Project

## 1. Clone Repository

```bash
git clone https://github.com/USERNAME/Bank-Rongsok.git
cd Bank-Rongsok
```

---

## 2. Setup Backend Laravel

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### Setup Database

Edit file `.env`

```env
DB_DATABASE=bank_rongsok
DB_USERNAME=root
DB_PASSWORD=
```

### Jalankan Migration

```bash
php artisan migrate
```

### Jalankan Storage Link

```bash
php artisan storage:link
```

### Jalankan Laravel Server

```bash
php artisan serve
```

---

## 3. Setup Frontend Vue

```bash
cd frontend
npm install
npm run dev
```

---

# 📧 Konfigurasi Gmail OTP

Tambahkan konfigurasi berikut pada file `.env` Laravel:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Bank Rongsok"
```

> Gunakan Gmail App Password, bukan password Gmail biasa.

---

# 📈 API Features

## Authentication

* POST `/api/register`
* POST `/api/login`
* POST `/api/verify-otp`
* POST `/api/resend-otp`
* POST `/api/logout`

## Dashboard

* GET `/api/dashboard/analytics`
* GET `/api/dashboard/monthly-revenue`
* GET `/api/dashboard/recent-activities`
* GET `/api/dashboard/recent-distributions`

## Activities

* CRUD Activities
* Upload Activity Images
* Delete Images

---

# 🚀 Keunggulan Project

✅ Clean Architecture

✅ REST API Ready

✅ Responsive UI

✅ OTP Authentication

✅ Image Optimization WebP

✅ Dashboard Analytics

✅ Modern Admin Panel

✅ Scalable Structure

---

# 📌 Future Improvements

* Notifikasi realtime
* Export laporan PDF
* Sistem role & permission
* PWA support
* Mobile app integration
* AI analytics recommendation

---

# 👨‍💻 Developer

### Gilang Ardiansyah

📧 Email: [ardiansyahgilang894@gmail.com](mailto:ardiansyahgilang894@gmail.com)

🌐 GitHub: [https://github.com/ardiansyahgilang894-coder](https://github.com/ardiansyahgilang894-coder)

---

# ⭐ Support

Jika project ini membantu, jangan lupa berikan:

⭐ Star repository ini di GitHub

🤝 Contribution & feedback sangat terbuka

---

<div align="center">

### ♻️ Membangun Lingkungan Bersih Melalui Teknologi Digital

</div>
