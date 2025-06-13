<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Konsultasi Klinik</title>
    <style>
        /* CSS Umum */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align ke atas agar judul tidak terlalu di tengah jika konten sedikit */
            min-height: 100vh;
        }

        .container {
            width: 95%;
            max-width: 1200px; /* Lebar maksimal disesuaikan untuk tabel konsultasi yang lebih lebar */
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px; /* Jarak dari atas */
            margin-bottom: 30px; /* Jarak dari bawah */
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 2.2em;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
            width: 100%;
        }

        /* Navigasi Aksi (Buat Konsultasi, Kembali) */
        .action-buttons {
            margin-bottom: 20px;
            text-align: right; /* Tombol aksi di sebelah kanan */
        }

        .action-buttons a {
            text-decoration: none;
            color: #ffffff;
            font-weight: bold;
            padding: 10px 18px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
            margin-left: 10px; /* Jarak antar tombol */
        }

        .create-button {
            background-color: #28a745; /* Hijau */
            border: 1px solid #28a745;
        }

        .create-button:hover {
            background-color: #218838;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
            transform: translateY(-2px);
        }

        .back-button {
            background-color: #6c757d; /* Abu-abu */
            border: 1px solid #6c757d;
        }

        .back-button:hover {
            background-color: #5a6268;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.4);
            transform: translateY(-2px);
        }

        /* Styling Tabel */
        table {
            width: 100%;
            border-collapse: separate; /* Gunakan separate untuk border-radius pada sel */
            border-spacing: 0;
            margin-top: 20px;
            border-radius: 8px; /* Border radius pada tabel secara keseluruhan */
            overflow: hidden; /* Penting agar border-radius berfungsi pada anak elemen */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0; /* Border kanan untuk pemisah kolom */
        }

        th {
            background-color: #3498db; /* Warna biru untuk header tabel */
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        /* Hilangkan border kanan pada kolom terakhir header dan data */
        th:last-child, td:last-child {
            border-right: none;
        }

        /* Border radius untuk sudut header tabel */
        th:first-child {
            border-top-left-radius: 8px;
        }
        th:last-child {
            border-top-right-radius: 8px;
        }

        /* Gaya baris genap/ganjil */
        tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tbody tr:hover {
            background-color: #eef7fc; /* Warna hover yang lembut */
        }

        /* Hilangkan border bawah pada baris terakhir */
        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Styling Link Aksi dalam Tabel */
        .action-links a {
            margin-right: 8px; /* Sedikit lebih kecil karena ada 3 link */
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.2s ease, color 0.2s ease;
            white-space: nowrap; /* Pastikan link tidak pecah baris */
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .action-links a[href*="view.php"] {
            color: #007bff; /* Biru untuk Lihat Detail */
            border: 1px solid #007bff;
        }
        .action-links a[href*="view.php"]:hover {
            background-color: #007bff;
            color: white;
        }

        .action-links a[href*="edit.php"] {
            color: #ffc107; /* Kuning untuk Edit */
            border: 1px solid #ffc107;
        }
        .action-links a[href*="edit.php"]:hover {
            background-color: #ffc107;
            color: white;
        }

        .action-links a[href*="delete.php"] {
            color: #dc3545; /* Merah untuk Hapus */
            border: 1px solid #dc3545;
        }
        .action-links a[href*="delete.php"]:hover {
            background-color: #dc3545;
            color: white;
        }

        /* Responsif */
        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
                margin-top: 20px;
            }
            .action-buttons {
                text-align: center; /* Tombol aksi di tengah pada layar kecil */
                margin-bottom: 15px;
            }
            .action-buttons a {
                margin: 5px; /* Jarak antar tombol pada layar kecil */
                width: calc(50% - 10px); /* Membuat dua tombol berdampingan */
                text-align: center;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }

            /* Sembunyikan header tabel asli */
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #ccc;
                margin-bottom: 10px;
                border-radius: 8px;
                overflow: hidden;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%; /* Ruang untuk pseudo-elemen label */
                text-align: right;
            }

            td:before {
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #555;
            }

            /* Label untuk setiap kolom pada mode responsif */
            td:nth-of-type(1):before { content: "ID Konsultasi:"; }
            td:nth-of-type(2):before { content: "Tanggal Konsultasi:"; }
            td:nth-of-type(3):before { content: "Pasien:"; }
            td:nth-of-type(4):before { content: "Dokter:"; }
            td:nth-of-type(5):before { content: "Deskripsi:"; }
            td:nth-of-type(6):before { content: "Diagnosa:"; }
            td:nth-of-type(7):before { content: "Status:"; }
            td:nth-of-type(8):before { content: "Aksi:"; }

            td:last-child {
                border-bottom: none;
            }

            .action-links {
                display: flex; /* Menggunakan flexbox untuk link aksi */
                flex-wrap: wrap; /* Memungkinkan link untuk wrap ke baris baru */
                justify-content: flex-end; /* Ratakan ke kanan */
                gap: 5px; /* Jarak antar link */
            }
            .action-links a {
                flex: 1 1 auto; /* Memungkinkan link untuk tumbuh dan menyusut */
                min-width: 80px; /* Lebar minimum untuk setiap link */
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Konsultasi Klinik</h2>

        <div class="action-buttons">
            <a href="create.php" class="create-button">Buat Konsultasi Baru</a>
            <a href="../index.php" class="back-button">Kembali ke Beranda</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Konsultasi</th>
                    <th>Tanggal Konsultasi</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Diagnosa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
$result = $conn->query("
    SELECT
        K.id_konsultasi,
        K.tanggal_konsultasi,
        P.nama_pasien,
        D.nama_dokter,
        DI.nama_diagnosa
    FROM
        Konsultasi AS K
    JOIN
        Pasien AS P ON K.id_pasien = P.id_pasien
    JOIN
        Dokter AS D ON K.id_dokter = D.id_dokter
    LEFT JOIN
        Diagnosa AS DI ON K.id_diagnosa = DI.id_diagnosa
    ORDER BY K.tanggal_konsultasi DESC
");


if(!$result) {
    echo "<tr><td colspan='8' style='color: red; text-align: center;'>Query error: " . htmlspecialchars($conn->error) . "</td></tr>";
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= $row['id_konsultasi']; ?></td>
    <td><?= $row['tanggal_konsultasi']; ?></td>
    <td><?= htmlspecialchars($row['nama_pasien']); ?></td>
    <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
    <td><?= htmlspecialchars($row['nama_diagnosa'] ?? '-'); ?></td>
    <td class="action-links">
        <a href="view.php?id=<?= $row['id_konsultasi']; ?>" class="view-link">Lihat Detail</a>
        <a href="edit.php?id=<?= $row['id_konsultasi']; ?>" class="edit-link">Edit</a>
        <a href="delete.php?id=<?= $row['id_konsultasi']; ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus konsultasi ini? Data obat terkait juga akan dihapus.')">Hapus</a>
    </td>
</tr>
<?php
    endwhile;
} else {
    echo "<tr><td colspan='8' style='text-align: center;'>Tidak ada data konsultasi.</td></tr>";
}
?>

            </tbody>
        </table>
    </div>
</body>
</html>