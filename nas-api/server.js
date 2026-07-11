/**
 * Koperasi Merah Putih Sidorejo - NAS REST API Server
 * 
 * Server ini berjalan pada Android TV Box ZTE ZXV10 B860H (Armbian Linux)
 * untuk melayani proses upload, download, dan delete dokumen koperasi ke External HDD.
 */

const express = require('express');
const multer = require('multer');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

const app = express();

// Konfigurasi port dan lokasi penyimpanan melalui Environment Variables atau default
const PORT = process.env.PORT || 3000;
const STORAGE_PATH = process.env.STORAGE_PATH || '/mnt/external_hdd/koperasi_files';
const API_KEY = process.env.API_KEY || 'rahasia_api_key_anda'; // Pengaman akses API

// Memastikan folder penyimpanan eksternal HDD sudah ada secara rekursif
if (!fs.existsSync(STORAGE_PATH)) {
    try {
        fs.mkdirSync(STORAGE_PATH, { recursive: true });
        console.log(`[INFO] Folder penyimpanan berhasil dibuat di: ${STORAGE_PATH}`);
    } catch (err) {
        console.error(`[ERROR] Gagal membuat folder penyimpanan di ${STORAGE_PATH}:`, err.message);
    }
}

// Konfigurasi CORS: Mengizinkan domain frontend mengakses API ini
const allowedOrigins = process.env.ALLOWED_ORIGINS ? process.env.ALLOWED_ORIGINS.split(',') : ['*'];
app.use(cors({
    origin: function (origin, callback) {
        // Mengizinkan tanpa origin (seperti curl atau server-side Laravel)
        if (!origin) return callback(null, true);
        if (allowedOrigins.indexOf('*') !== -1 || allowedOrigins.indexOf(origin) !== -1) {
            return callback(null, true);
        }
        return callback(new Error('Akses CORS ditolak oleh kebijakan keamanan NAS.'), false);
    }
}));

app.use(express.json());

// Middleware Keamanan: Validasi API Key
const authenticateApiKey = (req, res, next) => {
    const requestKey = req.headers['x-api-key'] || req.query.api_key;
    if (requestKey && requestKey === API_KEY) {
        return next();
    }
    return res.status(401).json({
        status: 'error',
        message: 'Akses tidak sah. API Key tidak valid atau tidak disertakan.'
    });
};

// Konfigurasi Multer untuk penanganan upload file
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, STORAGE_PATH);
    },
    filename: function (req, file, cb) {
        // Sanitasi nama file untuk menghindari karakter berbahaya dan directory traversal
        const originalName = file.originalname;
        const sanitized = Date.now() + '_' + path.basename(originalName).replace(/[^a-zA-Z0-9._-]/g, '_');
        cb(null, sanitized);
    }
});

const upload = multer({
    storage: storage,
    limits: {
        fileSize: 20 * 1024 * 1024 // Batas ukuran berkas 20MB
    }
});

/**
 * 1. ENDPOINT UPLOAD FILE
 * Route: POST /api/v1/arsip/upload
 * Menerima file tunggal dengan nama field 'file_upload' beserta metadata tambahan.
 */
app.post('/api/v1/arsip/upload', authenticateApiKey, upload.single('file_upload'), (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({
                status: 'error',
                message: 'Tidak ada berkas yang dikirimkan.'
            });
        }

        // Opsional: mengambil metadata kategori & pengunggah jika dikirim dari frontend
        const kategori = req.body.kategori || 'Umum';
        const pengunggah = req.body.uploader || 'System';

        console.log(`[UPLOAD] Berkas diterima: ${req.file.filename} | Kategori: ${kategori} | Oleh: ${pengunggah}`);

        return res.status(200).json({
            status: 'success',
            message: 'Berkas berhasil diunggah dan disimpan ke server NAS',
            filename: req.file.filename,
            original_name: req.file.originalname,
            path: path.join(STORAGE_PATH, req.file.filename),
            size: req.file.size,
            mime_type: req.file.mimetype
        });
    } catch (error) {
        console.error('[UPLOAD ERROR]', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal memproses unggahan file pada NAS server.',
            error: error.message
        });
    }
});

/**
 * 2. ENDPOINT DOWNLOAD FILE
 * Route: GET /api/v1/arsip/download/:filename
 * Mengalirkan/download berkas fisik yang tersimpan di HDD eksternal.
 */
app.get('/api/v1/arsip/download/:filename', authenticateApiKey, (req, res) => {
    try {
        // Sanitasi parameter filename untuk menghindari Directory Traversal attack
        const safeFilename = path.basename(req.params.filename);
        const filePath = path.join(STORAGE_PATH, safeFilename);

        // Periksa apakah file fisik ada di HDD eksternal
        if (!fs.existsSync(filePath)) {
            console.log(`[DOWNLOAD] File tidak ditemukan: ${safeFilename}`);
            return res.status(404).json({
                status: 'error',
                message: 'Berkas tidak ditemukan di penyimpanan server NAS.'
            });
        }

        console.log(`[DOWNLOAD] Mengalirkan file: ${safeFilename}`);
        
        // Kirim file sebagai download attachment ke client
        return res.download(filePath, safeFilename, (err) => {
            if (err) {
                // Jika transfer terputus di tengah jalan oleh client, log saja tanpa double-res
                if (!res.headersSent) {
                    return res.status(500).json({
                        status: 'error',
                        message: 'Terjadi kesalahan saat mengalirkan berkas.',
                        error: err.message
                    });
                }
            }
        });
    } catch (error) {
        console.error('[DOWNLOAD ERROR]', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengunduh berkas dari NAS.',
            error: error.message
        });
    }
});

/**
 * 3. ENDPOINT DELETE FILE (TAMBAHAN)
 * Route: DELETE /api/v1/arsip/:filename
 * Menghapus berkas dari HDD eksternal secara fisik.
 */
app.delete('/api/v1/arsip/:filename', authenticateApiKey, (req, res) => {
    try {
        const safeFilename = path.basename(req.params.filename);
        const filePath = path.join(STORAGE_PATH, safeFilename);

        if (!fs.existsSync(filePath)) {
            return res.status(404).json({
                status: 'error',
                message: 'Berkas tidak ditemukan, penghapusan dibatalkan.'
            });
        }

        // Hapus file secara sinkron
        fs.unlinkSync(filePath);
        console.log(`[DELETE] Berkas berhasil dihapus: ${safeFilename}`);

        return res.status(200).json({
            status: 'success',
            message: 'Berkas fisik berhasil dihapus dari server NAS'
        });
    } catch (error) {
        console.error('[DELETE ERROR]', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal menghapus berkas fisik dari NAS.',
            error: error.message
        });
    }
});

/**
 * 4. ENDPOINT HEALTH CHECK
 * Route: GET /api/v1/arsip/status
 * Memeriksa status operasional server NAS dan status read/write HDD eksternal.
 */
app.get('/api/v1/arsip/status', (req, res) => {
    let storageWritable = false;
    let storageReadable = false;

    try {
        // Cek apakah directory terbaca
        fs.accessSync(STORAGE_PATH, fs.constants.R_OK);
        storageReadable = true;

        // Cek writeability dengan menulis file temporary kecil lalu menghapusnya kembali
        const tempTestFile = path.join(STORAGE_PATH, `.write_test_${Date.now()}.tmp`);
        fs.writeFileSync(tempTestFile, 'test');
        fs.unlinkSync(tempTestFile);
        storageWritable = true;
    } catch (err) {
        console.error('[HEALTHCHECK ERROR] Gagal mengakses HDD eksternal:', err.message);
    }

    const serverStatus = {
        status: (storageReadable && storageWritable) ? 'healthy' : 'unhealthy',
        timestamp: new Date().toISOString(),
        system: {
            platform: process.platform,
            arch: process.arch,
            uptime: process.uptime()
        },
        storage: {
            path: STORAGE_PATH,
            exists: fs.existsSync(STORAGE_PATH),
            readable: storageReadable,
            writable: storageWritable
        }
    };

    const statusCode = serverStatus.status === 'healthy' ? 200 : 503;
    return res.status(statusCode).json(serverStatus);
});

// Global Error Handling Middleware
app.use((err, req, res, next) => {
    console.error('[GLOBAL ERROR]', err);
    return res.status(500).json({
        status: 'error',
        message: 'Terjadi kesalahan sistem internal pada NAS server.',
        error: err.message
    });
});

// Start Server
app.listen(PORT, '0.0.0.0', () => {
    console.log(`====================================================`);
    console.log(` KOPERASI MERAH PUTIH SIDOREJO - NAS API SERVER     `);
    console.log(` Running on  : http://0.0.0.0:${PORT}              `);
    console.log(` Storage Path: ${STORAGE_PATH}                     `);
    console.log(`====================================================`);
});
