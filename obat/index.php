<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Obat</title>
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
            max-width: 1000px; /* Lebar maksimal disesuaikan untuk tabel */
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

        /* Navigasi Aksi (Tambah, Kembali) */
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

        .add-button {
            background-color: #28a745; /* Hijau */
            border: 1px solid #28a745;
        }

        .add-button:hover {
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
            margin-right: 10px;
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .action-links a[href*="edit.php"] {
            color: #007bff; /* Biru untuk Edit */
            border: 1px solid #007bff;
        }
        .action-links a[href*="edit.php"]:hover {
            background-color: #007bff;
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
            td:nth-of-type(1):before { content: "ID Obat:"; }
            td:nth-of-type(2):before { content: "Nama Obat:"; }
            td:nth-of-type(3):before { content: "Satuan:"; }
            td:nth-of-type(4):before { content: "Harga Satuan:"; }
            td:nth-of-type(5):before { content: "Aksi:"; }

            td:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Obat</h2>

        <div class="action-buttons">
            <a href="create.php" class="add-button">Tambah Obat Baru</a>
            <a href="../index.php" class="back-button">Kembali ke Beranda</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Obat</th>
                    <th>Nama Obat</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Obat ORDER BY nama_obat ASC");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id_obat']; ?></td>
                    <td><?= htmlspecialchars($row['nama_obat']); ?></td>
                    <td><?= htmlspecialchars($row['satuan']); ?></td>
                    <td>Rp <?= number_format($row['harga_satuan'], 2, ',', '.'); ?></td>
                    <td class="action-links">
                        <a href="edit.php?id=<?= $row['id_obat']; ?>">Edit</a>
                        <a href="delete.php?id=<?= $row['id_obat']; ?>" onclick="return confirm('Yakin ingin menghapus obat ini? Data konsultasi terkait akan terpengaruh.')">Hapus</a>
                    </td>
                </tr>
                <?php
                    endwhile;
                } else {
                    echo "<tr><td colspan='5' style='text-align: center;'>Tidak ada data obat.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>