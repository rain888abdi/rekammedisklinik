<!DOCTYPE html>
<html>
<head>
    <title>Sistem Rekam Medis Klinik</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
        }
        .header {
            background-color: #28a745; /* Warna hijau cerah */
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .container {
            max-width: 960px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1em;
            color: #555;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            justify-content: center;
        }
        .menu-item {
            background-color: #e2f0e6; /* Hijau muda */
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 120px;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: #d1ead5; /* Sedikit lebih gelap saat hover */
        }
        .menu-item h3 {
            margin-top: 10px;
            margin-bottom: 5px;
            color: #28a745; /* Warna hijau cerah */
        }
        .menu-item p {
            font-size: 0.9em;
            color: #666;
        }
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #777;
            font-size: 0.9em;
            background-color: #f0f0f0;
            border-top: 1px solid #e0e0e0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
            .container {
                margin: 20px;
                padding: 15px;
            }
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sistem Rekam Medis Klinik</h1>
    </div>

    <div class="container">
        <p class="welcome-text">Selamat datang di sistem manajemen rekam medis klinik. Pilih modul di bawah ini untuk memulai.</p>

        <div class="menu-grid">
            <a href="pasien/index.php" class="menu-item">
                <h3>Manajemen Pasien</h3>
                <p>Tambah, lihat, edit, dan hapus data pasien.</p>
            </a>
            <a href="dokter/index.php" class="menu-item">
                <h3>Manajemen Dokter</h3>
                <p>Kelola informasi dokter yang bekerja di klinik.</p>
            </a>
            <a href="jadwal/index.php" class="menu-item">
                <h3>Manajemen Jadwal Dokter</h3>
                <p>Atur dan lihat jadwal praktik dokter.</p>
            </a>
            <a href="diagnosa/index.php" class="menu-item">
                <h3>Manajemen Diagnosa</h3>
                <p>Daftar dan kelola jenis diagnosa penyakit.</p>
            </a>
            <a href="obat/index.php" class="menu-item">
                <h3>Manajemen Obat</h3>
                <p>Input dan update daftar obat yang tersedia.</p>
            </a>
            <a href="konsultasi/index.php" class="menu-item">
                <h3>Manajemen Konsultasi</h3>
                <p>Catat detail konsultasi, diagnosa, dan resep obat.</p>
            </a>
            <a href="laporan/rekammedis.php" class="menu-item">
                <h3>Rekam Medis </h3>
                <p>Lihat Rekam Medis Pasien.</p>
            </a>
            <a href="laporan/rekap_pasien_per_dokter.php" class="menu-item">
                <h3>Laporan</h3>
                <p>Lihat rekapitulasi data dan statistik.</p>
            </a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Sistem Rekam Medis Klinik. All rights reserved.</p>
    </div>

</body>
</html>