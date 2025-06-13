<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Konsultasi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px;}
        .container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .detail-group { margin-bottom: 15px; }
        .detail-group label { font-weight: bold; display: inline-block; width: 150px; }
        .detail-group span { display: inline-block; }
        ul { list-style: none; padding: 0; }
        ul li { margin-bottom: 5px; }
        .back-link { display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Konsultasi</h2>
        <?php
        $id_konsultasi = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id_konsultasi === 0) {
            header("Location: index.php");
            exit;
        }

        $stmt_konsultasi = $conn->prepare("
            SELECT 
                K.id_konsultasi,
                K.tanggal_konsultasi,
                K.id_pasien,
                P.nama_pasien,
                P.tanggal_lahir AS tgl_lahir_pasien,
                P.jenis_kelamin AS jk_pasien,
                P.alamat AS alamat_pasien,
                P.telepon AS telp_pasien,
                D.nama_dokter,
                D.spesialisasi AS spesialisasi_dokter,
                K.Deskripsi,
                DI.nama_diagnosa,
                DI.deskripsi_diagnosa,
                K.catatan_dokter
            FROM 
                Konsultasi AS K
            JOIN 
                Pasien AS P ON K.id_pasien = P.id_pasien
            JOIN 
                Dokter AS D ON K.id_dokter = D.id_dokter
            LEFT JOIN
                Diagnosa AS DI ON K.id_diagnosa = DI.id_diagnosa
            WHERE 
                K.id_konsultasi = ?
        ");
        
        if (!$stmt_konsultasi) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt_konsultasi->bind_param("i", $id_konsultasi);
        $stmt_konsultasi->execute();
        $result_konsultasi = $stmt_konsultasi->get_result();
        $konsultasi = $result_konsultasi->fetch_assoc();
        $stmt_konsultasi->close();

        $id_pasien = $konsultasi['id_pasien']; // ambil ID pasien dari hasil konsultasi

// Ambil semua riwayat konsultasi untuk pasien ini
$stmt_riwayat = $conn->prepare("
    SELECT 
        K.tanggal_konsultasi,
        D.nama_dokter,
        DI.nama_diagnosa,
        K.catatan_dokter
    FROM Konsultasi K
    JOIN Dokter D ON K.id_dokter = D.id_dokter
    LEFT JOIN Diagnosa DI ON K.id_diagnosa = DI.id_diagnosa
    WHERE K.id_pasien = ?
    ORDER BY K.tanggal_konsultasi DESC
");
$stmt_riwayat->bind_param("i", $id_pasien);
$stmt_riwayat->execute();
$riwayat_result = $stmt_riwayat->get_result();
$stmt_riwayat->close();


        if (!$konsultasi) {
            echo "Konsultasi tidak ditemukan.";
            exit;
        }

        $stmt_obat = $conn->prepare("
            SELECT 
                O.nama_obat,
                O.kategori,
                DCO.jumlah,
                DCO.instruksi_pemakaian
            FROM 
                DetailKonsultasiObat AS DCO
            JOIN 
                Obat AS O ON DCO.id_obat = O.id_obat
            WHERE 
                DCO.id_konsultasi = ?
        ");
        $stmt_obat->bind_param("i", $id_konsultasi);
        $stmt_obat->execute();
        $result_obat = $stmt_obat->get_result();
        $obats_diberikan = $result_obat->fetch_all(MYSQLI_ASSOC);
        $stmt_obat->close();
        ?>

        <div class="detail-group">
            <label>ID Konsultasi:</label>
            <span><?= $konsultasi['id_konsultasi']; ?></span>
        </div>
        <div class="detail-group">
            <label>Tanggal Konsultasi:</label>
            <span><?= date('d-m-Y H:i', strtotime($konsultasi['tanggal_konsultasi'])); ?></span>
        </div>

        <h3>Informasi Pasien</h3>
        <div class="detail-group">
            <label>Nama Pasien:</label>
            <span><?= htmlspecialchars($konsultasi['nama_pasien']); ?></span>
        </div>
        <div class="detail-group">
            <label>Tanggal Lahir:</label>
            <span><?= htmlspecialchars($konsultasi['tgl_lahir_pasien']); ?></span>
        </div>
        <div class="detail-group">
            <label>Jenis Kelamin:</label>
            <span><?= htmlspecialchars($konsultasi['jk_pasien']); ?></span>
        </div>
        <div class="detail-group">
            <label>Alamat:</label>
            <span><?= htmlspecialchars($konsultasi['alamat_pasien']); ?></span>
        </div>
        <div class="detail-group">
            <label>Telepon:</label>
            <span><?= htmlspecialchars($konsultasi['telp_pasien']); ?></span>
        </div>

        <h3>Informasi Dokter</h3>
        <div class="detail-group">
            <label>Nama Dokter:</label>
            <span><?= htmlspecialchars($konsultasi['nama_dokter']); ?></span>
        </div>
        <div class="detail-group">
            <label>Spesialisasi:</label>
            <span><?= htmlspecialchars($konsultasi['spesialisasi_dokter']); ?></span>
        </div>
        
        <h3>Detail Konsultasi</h3>
        <div class="detail-group">
            <label>Diagnosa:</label>
            <span><?= htmlspecialchars($konsultasi['nama_diagnosa'] ?? '-'); ?></span>
        </div>
        <div class="detail-group">
            <label>Deskripsi Diagnosa:</label>
            <span><?= nl2br(htmlspecialchars($konsultasi['Deskripsi'])); ?></span>
        </div>
        <div class="detail-group">
            <label>Catatan Dokter:</label>
            <span><?= nl2br(htmlspecialchars($konsultasi['catatan_dokter'])); ?></span>
        </div>
        <h3>Obat yang Diberikan</h3>
        <?php if (!empty($obats_diberikan)): ?>
            <ul>
                <?php foreach ($obats_diberikan as $obat): ?>
                    <li>
                        <?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['jumlah']); ?> <?= htmlspecialchars($obat['kategori']); ?>) - Instruksi: <?= htmlspecialchars($obat['instruksi_pemakaian']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Tidak ada obat yang diresepkan untuk konsultasi ini.</p>
        <?php endif; ?>
        
<h3>Riwayat Rekam Medis Pasien Ini</h3>
<?php if ($riwayat_result->num_rows > 0): ?>
    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top: 15px;">
        <thead style="background-color: #4CAF50; color: white;">
            <tr>
                <th>Tanggal</th>
                <th>Dokter</th>
                <th>Diagnosa</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($riwayat = $riwayat_result->fetch_assoc()): ?>
                <tr>
                    <td><?= date('d-m-Y H:i', strtotime($riwayat['tanggal_konsultasi'])) ?></td>
                    <td><?= htmlspecialchars($riwayat['nama_dokter']) ?></td>
                    <td><?= htmlspecialchars($riwayat['nama_diagnosa'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($riwayat['catatan_dokter']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><em>Belum ada riwayat konsultasi lainnya untuk pasien ini.</em></p>
<?php endif; ?>
        

        <div class="back-link">
            <a href="index.php">Kembali ke Daftar Konsultasi</a>
        </div>
    </div>
</body>
</html>
