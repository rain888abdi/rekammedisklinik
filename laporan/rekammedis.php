<?php
include '../db.php'; // koneksi ke database

// Ambil semua data konsultasi (sebagai rekam medis terakhir)
$query = "
    SELECT 
        K.tanggal_konsultasi,
        P.nama_pasien,
        D.nama_dokter,
        DI.nama_diagnosa,
        K.catatan_dokter
    FROM Konsultasi K
    JOIN Pasien P ON K.id_pasien = P.id_pasien
    JOIN Dokter D ON K.id_dokter = D.id_dokter
    LEFT JOIN Diagnosa DI ON K.id_diagnosa = DI.id_diagnosa
    ORDER BY K.tanggal_konsultasi DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f8f8; }
        h2 { text-align: center; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; background-color: white; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn-back {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>

<h2>Riwayat Rekam Medis</h2>

<table>
    <thead>
        <tr>
            <th>Pasien</th>
            <th>Dokter</th>
            <th>Diagnosa</th>
            <th>Catatan</th>
            <th>Tanggal Konsultasi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                    <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
                    <td><?= htmlspecialchars($row['nama_diagnosa'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['catatan_dokter']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal_konsultasi'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Belum ada data rekam medis tercatat.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align: center;">
    <a href="../index.php" class="btn-back">Kembali ke Beranda</a>
</div>

</body>
</html>
