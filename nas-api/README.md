# NAS REST API Server - Koperasi Merah Putih Sidorejo

Repositori ini berisi backend REST API server berbasis **Node.js Express** yang dirancang untuk berjalan pada hardware **Android TV Box ZTE ZXV10 B860H** dengan sistem operasi **Armbian Linux (headless)**. API ini bertugas mengelola penyimpanan dokumen koperasi secara terpusat pada **External HDD** melalui koneksi USB 2.0.

---

## 1. Persyaratan Sistem
*   **Perangkat**: ZTE ZXV10 B860H (Armbian OS)
*   **Penyimpanan**: HDD Eksternal yang terpasang (*mounted*) di direktori target (default: `/mnt/external_hdd/koperasi_files`)
*   **Runtime**: Node.js versi 14.x atau yang lebih baru beserta npm

---

## 2. Instalasi & Menjalankan API Server

### Langkah A: Persiapan Folder HDD
Pastikan folder target penyimpanan di HDD eksternal Anda sudah siap dan memiliki hak akses baca/tulis (*read/write permissions*):
```bash
sudo mkdir -p /extdrive/ext2/data/website_kopdes/storage
sudo chmod -R 775 /extdrive/ext2/data/website_kopdes/storage
sudo chown -R $USER:$USER /extdrive/ext2/data/website_kopdes/storage
```

### Langkah B: Instalasi Dependensi Node.js
Masuk ke folder `nas-api` di TV Box Anda, kemudian jalankan instalasi:
```bash
cd nas-api
npm install
```

### Langkah C: Menjalankan Server secara Manual
Anda dapat menjalankan server menggunakan *environment variables* kustom sesuai kebutuhan.

*   **Untuk Bash / Zsh (default Linux):**
    ```bash
    PORT=3000 STORAGE_PATH="/mnt/external_hdd/koperasi_files" API_KEY="biji" npm start
    ```
*   **Untuk Fish Shell:**
    ```fish
    env PORT=3000 STORAGE_PATH="/mnt/external_hdd/koperasi_files" API_KEY="biji" npm start
    ```
    *(Atau dengan menetapkannya terlebih dahulu: `set -x PORT 3000; set -x STORAGE_PATH "/mnt/external_hdd/koperasi_files"; set -x API_KEY "biji"; npm start`)*

---

## 3. Menjalankan Otomatis dengan PM2 (Rekomendasi)
Agar REST API server dapat otomatis berjalan ketika TV Box dinyalakan (*auto-boot*) dan melakukan restart otomatis jika terjadi error:

1.  Instal **PM2** secara global:
    ```bash
    sudo npm install -g pm2
    ```
2.  Jalankan server menggunakan PM2 dengan menyertakan *environment variables*:
    *   **Untuk Bash / Zsh:**
        ```bash
        PORT=3000 STORAGE_PATH="/mnt/external_hdd/koperasi_files" API_KEY="biji" pm2 start server.js --name "koperasi-nas-api"
        ```
    *   **Untuk Fish Shell:**
        ```fish
        env PORT=3000 STORAGE_PATH="/mnt/external_hdd/koperasi_files" API_KEY="biji" pm2 start server.js --name "koperasi-nas-api"
        ```
3.  Simpan konfigurasi proses agar dijalankan kembali saat sistem reboot:
    ```bash
    pm2 save
    pm2 startup
    ```
    *(Ikuti perintah yang muncul di terminal setelah mengetik `pm2 startup` untuk mengaktifkan service systemd)*.

---

## 4. Konfigurasi Konektivitas Aman (Tailscale Funnel)

Karena TV Box Anda berada di dalam jaringan lokal (tidak memiliki IP Publik) dan Alwaysdata berada di cloud internet, kita menggunakan **Tailscale Funnel** agar server Alwaysdata dapat mengakses REST API di TV Box secara aman menggunakan protokol HTTPS publik dengan domain permanen tanpa perlu melakukan *port forwarding* pada router rumah.

### Langkah A: Instalasi & Login Tailscale
1.  Instal Tailscale di TV Box ZTE (Armbian):
    ```bash
    curl -fsSL https://tailscale.com/install.sh | sh
    ```
2.  Hubungkan TV Box ke akun Tailscale Anda:
    ```bash
    sudo tailscale up
    ```
    *(Klik link login yang muncul di terminal untuk menyelesaikan otentikasi).*

### Langkah B: Aktifkan HTTPS Certificates & ACL
1.  Buka **Tailscale Admin Console** di browser.
2.  Masuk ke menu **DNS** -> cari bagian **HTTPS Certificates** -> klik **Enable HTTPS...** (gratis).
3.  Di menu **Access Control (ACL)**, pastikan fitur Funnel sudah diaktifkan untuk akun Anda dengan menambahkan konfigurasi berikut:
    ```json
    "nodeAttrs": {
        "funnel": [
            {
                "target": ["autogroup:member"],
                "attr": ["funnel"],
            },
        ],
    },
    ```

### Langkah C: Menjalankan Funnel secara Permanen di Latar Belakang
Jalankan perintah ini di TV Box agar terowongan port `3000` (Node.js) diaktifkan ke publik secara permanen dan otomatis berjalan saat booting:
1.  Daftarkan port lokal ke serve engine:
    ```bash
    sudo tailscale serve --bg 3000
    ```
2.  Buka jalur publik (Funnel):
    ```bash
    sudo tailscale funnel --bg 3000
    ```
3.  Cek status terowongan:
    ```bash
    sudo tailscale funnel status
    ```
    *(Catat URL HTTPS publik permanen yang diberikan, misalnya `https://armbian.tailcdb5ff.ts.net/`).*

---

## 5. Dokumentasi API Endpoint

Semua request wajib menyertakan header `X-API-KEY: <api_key_anda>` atau parameter query `?api_key=<api_key_anda>`.

### 1. Health Check
*   **Route**: `GET /api/v1/arsip/status`
*   **Autentikasi**: Tidak diperlukan (Publik)
*   **Response (200 OK)**:
    ```json
    {
      "status": "healthy",
      "timestamp": "2026-07-09T13:00:00.000Z",
      "system": { "platform": "linux", "arch": "arm" },
      "storage": { "path": "/mnt/external_hdd/koperasi_files", "exists": true, "readable": true, "writable": true }
    }
    ```

### 2. Upload File
*   **Route**: `POST /api/v1/arsip/upload`
*   **Headers**: `X-API-KEY: rahasia_api_key_anda`
*   **Body (multipart/form-data)**:
    *   `file_upload`: `[Berkas Fisik]`
    *   `kategori`: `Laporan` (opsional)
    *   `uploader`: `Admin` (opsional)
*   **Response (200 OK)**:
    ```json
    {
      "status": "success",
      "message": "Berkas berhasil diunggah dan disimpan ke server NAS",
      "filename": "171999999_laporan_keuangan.pdf",
      "original_name": "laporan_keuangan.pdf",
      "path": "/mnt/external_hdd/koperasi_files/171999999_laporan_keuangan.pdf"
    }
    ```

### 3. Download File
*   **Route**: `GET /api/v1/arsip/download/:filename`
*   **Headers**: `X-API-KEY: rahasia_api_key_anda`
*   **Response**: File Stream (sebagai lampiran unduhan).

### 4. Delete File
*   **Route**: `DELETE /api/v1/arsip/:filename`
*   **Headers**: `X-API-KEY: rahasia_api_key_anda`
*   **Response (200 OK)**:
    ```json
    {
      "status": "success",
      "message": "Berkas fisik berhasil dihapus dari server NAS"
    }
    ```
