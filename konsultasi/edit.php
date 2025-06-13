<?php
// Aktifkan error reporting untuk debugging selama pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php'; // Pastikan file ini bersih dari spasi atau output sebelum <?php

$id_konsultasi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_konsultasi === 0) {
    header("Location: index.php");
    exit;
}

// Persiapkan statement untuk mengambil data konsultasi
$stmt_konsultasi = $conn->prepare("SELECT * FROM Konsultasi WHERE id_konsultasi = ?");
if ($stmt_konsultasi === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_konsultasi->bind_param("i", $id_konsultasi);
$stmt_konsultasi->execute();
$result_konsultasi = $stmt_konsultasi->get_result();
$konsultasi = $result_konsultasi->fetch_assoc();
$stmt_konsultasi->close();

if (!$konsultasi) {
    echo "<p style='color: red; text-align: center;'>Konsultasi tidak ditemukan.</p>";
    exit;
}

// Ambil semua data master
$all_pasien = $conn->query("SELECT id_pasien, nama_pasien FROM Pasien ORDER BY nama_pasien ASC")->fetch_all(MYSQLI_ASSOC);
$all_dokters = $conn->query("SELECT id_dokter, nama_dokter FROM Dokter ORDER BY nama_dokter ASC")->fetch_all(MYSQLI_ASSOC);
$all_diagnosas = $conn->query("SELECT id_diagnosa, nama_diagnosa FROM Diagnosa ORDER BY nama_diagnosa ASC")->fetch_all(MYSQLI_ASSOC);
<<<<<<< HEAD
$all_obats = $conn->query("SELECT id_obat, nama_obat, satuan, harga_satuan FROM Obat ORDER BY nama_obat ASC")->fetch_all(MYSQLI_ASSOC);
=======
$all_obats = $conn->query("SELECT id_obat, nama_obat, kategori FROM Obat ORDER BY nama_obat ASC")->fetch_all(MYSQLI_ASSOC);
>>>>>>> 6bcfc52 (rekam medis klinik)

// Ambil obat yang sudah diberikan untuk konsultasi ini
$stmt_current_obats = $conn->prepare("SELECT id_obat, jumlah, instruksi_pemakaian FROM DetailKonsultasiObat WHERE id_konsultasi = ?");
if ($stmt_current_obats === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_current_obats->bind_param("i", $id_konsultasi);
$stmt_current_obats->execute();
$result_current_obats = $stmt_current_obats->get_result();
$current_obats_diberikan = $result_current_obats->fetch_all(MYSQLI_ASSOC);
$stmt_current_obats->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pasien = $_POST['id_pasien'];
    $id_dokter = $_POST['id_dokter'];
<<<<<<< HEAD
    $keluhan = $_POST['keluhan'];
=======
    $Deskripsi = $_POST['Deskripsi'];
>>>>>>> 6bcfc52 (rekam medis klinik)
    
    // Perbaikan utama: Mengatur id_diagnosa menjadi NULL jika kosong.
    // Pastikan kolom id_diagnosa di database Anda adalah INT dan NULLABLE.
    $id_diagnosa = !empty($_POST['id_diagnosa']) ? intval($_POST['id_diagnosa']) : null;
    
    $catatan_dokter = $_POST['catatan_dokter'];
    $selected_obats = isset($_POST['obat']) ? $_POST['obat'] : [];

    // --- DEBUGGING: Uncomment untuk melihat data POST ---
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";
    // exit; // Hentikan eksekusi sementara untuk melihat output var_dump
    // --- AKHIR DEBUGGING ---

    $conn->begin_transaction();

    try {
<<<<<<< HEAD
        $stmt_update_konsultasi = $conn->prepare("UPDATE Konsultasi SET id_pasien=?, id_dokter=?, keluhan=?, id_diagnosa=?, catatan_dokter=? WHERE id_konsultasi=?");
=======
        $stmt_update_konsultasi = $conn->prepare("UPDATE Konsultasi SET id_pasien=?, id_dokter=?, Deskripsi=?, id_diagnosa=?, catatan_dokter=? WHERE id_konsultasi=?");
>>>>>>> 6bcfc52 (rekam medis klinik)
        if ($stmt_update_konsultasi === false) {
            throw new mysqli_sql_exception("Failed to prepare update statement: " . $conn->error);
        }

        // Penyederhanaan bind_param: MySQLi dapat menangani NULL dengan tipe 'i'
        // asalkan kolom di DB adalah INT NULLABLE.
<<<<<<< HEAD
        $stmt_update_konsultasi->bind_param("iisisi", $id_pasien, $id_dokter, $keluhan, $id_diagnosa, $catatan_dokter, $id_konsultasi);
=======
        $stmt_update_konsultasi->bind_param("iisisi", $id_pasien, $id_dokter, $Deskripsi, $id_diagnosa, $catatan_dokter, $id_konsultasi);
>>>>>>> 6bcfc52 (rekam medis klinik)
        
        $stmt_update_konsultasi->execute();
        
        // --- DEBUGGING: Periksa apakah update berhasil pada tabel Konsultasi ---
        // if ($stmt_update_konsultasi->affected_rows === 0 && !$stmt_update_konsultasi->warning_count) {
        //     error_log("No rows updated for main consultation. Data might be the same or an issue occurred.");
        // } else if ($stmt_update_konsultasi->affected_rows > 0) {
        //     error_log("Konsultasi main data updated successfully.");
        // } else {
        //     error_log("Konsultasi update query executed but no rows affected. Warnings: " . $stmt_update_konsultasi->warning_count);
        // }
        // --- AKHIR DEBUGGING ---

        $stmt_update_konsultasi->close();

        // Hapus detail obat yang lama
        $stmt_delete_detail = $conn->prepare("DELETE FROM DetailKonsultasiObat WHERE id_konsultasi=?");
        if ($stmt_delete_detail === false) {
            throw new mysqli_sql_exception("Failed to prepare delete detail statement: " . $conn->error);
        }
        $stmt_delete_detail->bind_param("i", $id_konsultasi);
        $stmt_delete_detail->execute();
        $stmt_delete_detail->close();

        // Masukkan detail obat yang baru
        if (!empty($selected_obats)) {
            $stmt_insert_detail = $conn->prepare("INSERT INTO DetailKonsultasiObat (id_konsultasi, id_obat, jumlah, instruksi_pemakaian) VALUES (?, ?, ?, ?)");
            if ($stmt_insert_detail === false) {
                throw new mysqli_sql_exception("Failed to prepare insert detail statement: " . $conn->error);
            }
            foreach ($selected_obats as $obat_data) {
                $obat_id = intval($obat_data['id_obat']);
                $jumlah = intval($obat_data['jumlah']);
                $instruksi = $obat_data['instruksi'];
                // Pastikan hanya memasukkan obat yang valid dan memiliki jumlah >= 0
                if ($obat_id > 0 && $jumlah >= 0) {
                    $stmt_insert_detail->bind_param("iiis", $id_konsultasi, $obat_id, $jumlah, $instruksi);
                    $stmt_insert_detail->execute();
                }
            }
            $stmt_insert_detail->close();
        }

        $conn->commit();
        header("Location: index.php?status=success_update");
        exit();
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Tampilkan pesan error secara detail untuk debugging
        echo "<p style='color: red; text-align: center;'>Error updating consultation: " . htmlspecialchars($exception->getMessage()) . "</p>";
        // Atau log error ke file server untuk keamanan
        // error_log("Error updating consultation: " . $exception->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konsultasi Klinik - <?php echo htmlspecialchars($konsultasi['id_konsultasi']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4CAF50; /* Hijau */
            --primary-dark: #45a049;
            --secondary-color: #007bff; /* Biru */
            --secondary-dark: #0056b3;
            --accent-color: #f44336; /* Merah untuk hapus */
            --text-color: #333;
            --bg-color: #f8f9fa;
            --card-bg: #fff;
            --border-color: #e0e0e0;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-dark);
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: calc(100% - 20px); /* Adjust for padding */
            padding: 12px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box; /* Include padding in width */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .obat-section h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
        }

        .obat-item {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
            align-items: center;
            background-color: #fefefe;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .obat-item select,
        .obat-item input {
            flex: 1; /* Allow inputs to grow */
            margin-right: 10px;
            min-width: 120px; /* Minimum width for responsiveness */
            margin-bottom: 5px; /* Spacing for wrap */
        }
        .obat-item input[type="number"] {
            max-width: 100px; /* Limit width for quantity */
            flex-shrink: 0;
        }
        .obat-item input[type="text"] {
            min-width: 150px;
        }

        .obat-item button {
            background-color: var(--accent-color);
            margin-left: auto; /* Push button to the right */
            margin-bottom: 5px; /* Spacing for wrap */
        }
        .obat-item button:hover {
            background-color: #d32f2f;
        }

        .button-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .button-group button,
        .button-group a {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .button-group button[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
            border: none;
        }
        .button-group button[type="submit"]:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
        }

        .button-group button.add-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: fit-content;
        }
        .button-group button.add-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .button-group a.back-link {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .button-group a.back-link:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        /* Responsiveness */
        @media (max-width: 600px) {
            .container {
                margin: 20px 10px;
                padding: 20px;
            }
            .form-group label {
                width: 100%;
                margin-bottom: 5px;
            }
            .obat-item {
                flex-direction: column;
                align-items: stretch;
            }
            .obat-item select,
            .obat-item input {
                margin-right: 0;
                margin-bottom: 10px;
                width: 100%;
            }
            .obat-item input[type="number"] {
                max-width: 100%;
            }
            .obat-item button {
                margin-left: 0;
                width: 100%;
            }
            .button-group button,
            .button-group a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Konsultasi</h2>
        <form method="post">
            <div class="form-group">
                <label for="id_pasien">Pasien:</label>
                <select name="id_pasien" id="id_pasien" required>
                    <?php foreach ($all_pasien as $p): ?>
                        <option value="<?= $p['id_pasien']; ?>" <?= ($p['id_pasien'] == $konsultasi['id_pasien']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($p['nama_pasien']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_dokter">Dokter:</label>
                <select name="id_dokter" id="id_dokter" required>
                    <?php foreach ($all_dokters as $dokter): ?>
                        <option value="<?= $dokter['id_dokter']; ?>" <?= ($dokter['id_dokter'] == $konsultasi['id_dokter']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($dokter['nama_dokter']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
<<<<<<< HEAD
                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" id="keluhan" rows="3"><?= htmlspecialchars($konsultasi['keluhan']); ?></textarea>
            </div>
            <div class="form-group">
=======
>>>>>>> 6bcfc52 (rekam medis klinik)
                <label for="id_diagnosa">Diagnosa:</label>
                <select name="id_diagnosa" id="id_diagnosa">
                    <option value="">Belum Didiagnosa</option>
                    <?php foreach ($all_diagnosas as $diagnosa): ?>
                        <option value="<?= $diagnosa['id_diagnosa']; ?>" <?= ($diagnosa['id_diagnosa'] == $konsultasi['id_diagnosa']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($diagnosa['nama_diagnosa']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
<<<<<<< HEAD
=======
                <label for="Deskripsi">Deskripsi Diagnosa:</label>
                <textarea name="Deskripsi" id="Deskripsi" rows="3"><?= htmlspecialchars($konsultasi['Deskripsi']); ?></textarea>
            </div>
            <div class="form-group">
>>>>>>> 6bcfc52 (rekam medis klinik)
                <label for="catatan_dokter">Catatan Dokter:</label>
                <textarea name="catatan_dokter" id="catatan_dokter" rows="5"><?= htmlspecialchars($konsultasi['catatan_dokter']); ?></textarea>
            </div>

            <div class="obat-section">
                <h3>Obat yang Diberikan:</h3>
                <div id="obat-list">
                    <?php $obatCounter = 0; ?>
                    <?php if (!empty($current_obats_diberikan)): ?>
                        <?php foreach ($current_obats_diberikan as $obat_item): ?>
                            <div class="obat-item">
                                <select name="obat[<?= $obatCounter; ?>][id_obat]">
                                    <option value="">Pilih Obat</option>
                                    <?php foreach ($all_obats as $obat): ?>
                                        <option value="<?= $obat['id_obat']; ?>" <?= ($obat['id_obat'] == $obat_item['id_obat']) ? 'selected' : ''; ?>>
<<<<<<< HEAD
                                            <?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['satuan']); ?>)
=======
                                            <?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['kategori']); ?>)
>>>>>>> 6bcfc52 (rekam medis klinik)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                Jumlah: <input type="number" name="obat[<?= $obatCounter; ?>][jumlah]" value="<?= htmlspecialchars($obat_item['jumlah']); ?>" min="0">
                                Instruksi: <input type="text" name="obat[<?= $obatCounter; ?>][instruksi]" value="<?= htmlspecialchars($obat_item['instruksi_pemakaian']); ?>">
                                <button type="button" onclick="removeObat(this)">Hapus</button>
                            </div>
                            <?php $obatCounter++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="addObat()" class="add-button">Tambah Obat</button>
            </div>

            <div class="button-group">
                <button type="submit">Update Konsultasi</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Konsultasi</a>
            </div>
        </form>
    </div>

    <script>
        let obatCounter = <?= count($current_obats_diberikan); ?>; // Mulai counter dari jumlah obat yang sudah ada

        function addObat() {
            const obatList = document.getElementById('obat-list');
            const newObatItem = document.createElement('div');
            newObatItem.classList.add('obat-item');
            newObatItem.innerHTML = `
                <select name="obat[${obatCounter}][id_obat]">
                    <option value="">Pilih Obat</option>
                    <?php foreach ($all_obats as $obat): ?>
<<<<<<< HEAD
                        <option value="<?= $obat['id_obat']; ?>"><?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['satuan']); ?>)</option>
=======
                        <option value="<?= $obat['id_obat']; ?>"><?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['kategori']); ?>)</option>
>>>>>>> 6bcfc52 (rekam medis klinik)
                    <?php endforeach; ?>
                </select>
                Jumlah: <input type="number" name="obat[${obatCounter}][jumlah]" value="0" min="0">
                Instruksi: <input type="text" name="obat[${obatCounter}][instruksi]">
                <button type="button" onclick="removeObat(this)">Hapus</button>
            `;
            obatList.appendChild(newObatItem);
            obatCounter++;
        }

        function removeObat(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>