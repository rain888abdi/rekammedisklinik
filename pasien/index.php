<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pasien</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        .nav-links {
            text-align: center;
            margin-bottom: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
            padding: 10px 15px;
            border: 1px solid #3498db;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 0 10px;
            display: inline-block;
        }

        .nav-links a:hover {
            background-color: #3498db;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden; /* Ensures rounded corners apply to table content */
        }

        th, td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
            transform: translateY(-2px);
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .action-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .action-links a:hover {
            color: #e74c3c; /* A slightly more pronounced hover for actions */
        }

        .action-links a.delete-link {
            color: #e74c3c;
        }

        .action-links a.delete-link:hover {
            color: #c0392b;
        }

        /* Responsive Table */
        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #e0e0e0;
                margin-bottom: 10px;
                border-radius: 8px;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
            }

            td:nth-of-type(1):before { content: "ID Pasien"; }
            td:nth-of-type(2):before { content: "Nama Pasien"; }
            td:nth-of-type(3):before { content: "Tanggal Lahir"; }
            td:nth-of-type(4):before { content: "Jenis Kelamin"; }
            td:nth-of-type(5):before { content: "Alamat"; }
            td:nth-of-type(6):before { content: "Telepon"; }
            td:nth-of-type(7):before { content: "Aksi"; }

            .action-links {
                text-align: right;
                padding-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Pasien</h2>
        <div class="nav-links">
            <a href="create.php">Tambah Pasien Baru</a>
            <a href="../index.php">Kembali ke Beranda</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Pasien</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Pasien ORDER BY nama_pasien ASC");
                if (!$result) {
                    die("Query Error: " . $conn->error);
                }
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id_pasien']; ?></td>
                    <td><?= htmlspecialchars($row['nama_pasien']); ?></td>
                    <td><?= htmlspecialchars($row['tanggal_lahir']); ?></td>
                    <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                    <td><?= htmlspecialchars(substr($row['alamat'], 0, 50)); ?><?= (strlen($row['alamat']) > 50) ? '...' : ''; ?></td>
                    <td><?= htmlspecialchars($row['telepon']); ?></td>
                    <td class="action-links">
                        <a href="edit.php?id=<?= $row['id_pasien']; ?>">Edit</a>
                        <a href="delete.php?id=<?= $row['id_pasien']; ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus pasien ini? Semua konsultasi terkait juga akan dihapus.')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>