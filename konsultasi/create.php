<?php
// Pastikan tidak ada spasi, baris kosong, atau karakter lain sebelum tag ini.
// Baris ini harus menjadi baris pertama dan tidak ada yang lain sebelumnya.
include '../db.php';

$error_message = ''; // Inisialisasi pesan error
$success_message = ''; // Inisialisasi pesan sukses

// Fetch all necessary data for dropdowns beforehand
$all_pasien = $conn->query("SELECT id_pasien, nama_pasien FROM Pasien ORDER BY nama_pasien ASC")->fetch_all(MYSQLI_ASSOC);
$all_dokters = $conn->query("SELECT id_dokter, nama_dokter FROM Dokter ORDER BY nama_dokter ASC")->fetch_all(MYSQLI_ASSOC);
$all_diagnosas = $conn->query("SELECT id_diagnosa, nama_diagnosa FROM Diagnosa ORDER BY nama_diagnosa ASC")->fetch_all(MYSQLI_ASSOC);
$all_obats = $conn->query("SELECT id_obat, nama_obat, satuan, harga_satuan FROM Obat ORDER BY nama_obat ASC")->fetch_all(MYSQLI_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pasien = $_POST['id_pasien'];
    $id_dokter = $_POST['id_dokter'];
    $keluhan = $_POST['keluhan'];
    $id_diagnosa = $_POST['id_diagnosa'] ?: null; // Handle if no diagnosis selected
    $catatan_dokter = $_POST['catatan_dokter'];
    $selected_obats = isset($_POST['obat']) ? $_POST['obat'] : [];

    // Validasi dasar
    if (empty($id_pasien) || empty($id_dokter) || empty($keluhan)) {
        $error_message = "Harap lengkapi semua bidang yang wajib diisi (Pasien, Dokter, Keluhan.";
    } else {
        $conn->begin_transaction(); // Mulai transaksi

        try {
            // Insert data konsultasi
            $stmt_konsultasi = $conn->prepare("INSERT INTO Konsultasi (id_pasien, id_dokter, keluhan, id_diagnosa, catatan_dokter) VALUES (?, ?, ?, ?, ?)");
            // 's' untuk string, 'i' untuk integer. Sesuaikan tipe data untuk bind_param
            // Jika id_diagnosa bisa NULL, pastikan tipe datanya sesuai (misal: 'i' jika int atau 's' jika string, dan tangani null dengan baik)
            $stmt_konsultasi->bind_param("iisis", $id_pasien, $id_dokter, $keluhan, $id_diagnosa, $catatan_dokter);
            $stmt_konsultasi->execute();
            $id_konsultasi = $stmt_konsultasi->insert_id;
            $stmt_konsultasi->close();

            // Insert detail obat jika ada
            if (!empty($selected_obats)) {
                $stmt_detail_obat = $conn->prepare("INSERT INTO DetailKonsultasiObat (id_konsultasi, id_obat, jumlah, instruksi_pemakaian) VALUES (?, ?, ?, ?)");
                foreach ($selected_obats as $obat_data) {
                    $obat_id = isset($obat_data['id_obat']) ? $obat_data['id_obat'] : 0;
                    $jumlah = isset($obat_data['jumlah']) ? intval($obat_data['jumlah']) : 0;
                    $instruksi = isset($obat_data['instruksi']) ? $obat_data['instruksi'] : '';

                    // Hanya masukkan jika obat valid dan jumlah lebih dari 0
                    if ($obat_id > 0 && $jumlah > 0) {
                        $stmt_detail_obat->bind_param("iiis", $id_konsultasi, $obat_id, $jumlah, $instruksi);
                        $stmt_detail_obat->execute();
                    }
                }
                $stmt_detail_obat->close();
            }

            $conn->commit(); // Commit transaksi jika semua berhasil
            $success_message = "Konsultasi baru berhasil ditambahkan!";
            // Redirect setelah beberapa detik atau tampilkan pesan sukses dan biarkan user klik link
            header("Location: index.php"); // Langsung redirect ke halaman daftar
            exit(); // Penting: Hentikan eksekusi script setelah redirect
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback(); // Rollback transaksi jika ada error
            $error_message = "Error: " . htmlspecialchars($exception->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Konsultasi Baru</title>
    <style>
        /* CSS umum untuk body */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container utama form */
        .container {
            width: 90%;
            max-width: 700px; /* Lebar maksimal disesuaikan untuk form yang lebih kompleks */
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px 0; /* Memberi sedikit margin atas/bawah */
        }

        /* Styling judul */
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

        h3 {
            color: #3498db;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 5px;
        }

        /* Setiap baris form (label + input/select/textarea) */
        form div {
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start; /* Align ke atas untuk textarea */
        }

        /* Styling label */
        label {
            flex: 0 0 160px; /* Lebar tetap untuk label */
            margin-right: 20px;
            font-weight: 600;
            color: #555;
            padding-top: 8px; /* Menyesuaikan posisi label dengan input */
        }

        /* Styling untuk input, select, textarea */
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            flex: 1; /* Mengambil sisa ruang */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            min-width: 150px; /* Lebar minimum agar tidak terlalu kecil */
        }

        textarea {
            resize: vertical; /* Hanya izinkan resize vertikal */
            min-height: 60px;
        }

        /* Efek fokus pada input/select/textarea */
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Styling untuk bagian obat yang diberikan */
        #obat-list {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fdfdfd;
        }

        .obat-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px; /* Jarak antar elemen dalam satu baris obat */
            flex-wrap: wrap; /* Memungkinkan wrap pada layar kecil */
        }

        .obat-item select {
            flex: 2; /* Mengambil lebih banyak ruang */
        }

        .obat-item input[type="number"] {
            width: 80px; /* Lebar tetap untuk jumlah */
            flex-grow: 0;
        }

        .obat-item input[type="text"] { /* Instruksi */
            flex: 3;
            min-width: 150px;
        }

        .obat-item button {
            background-color: #dc3545; /* Merah untuk hapus */
            padding: 8px 12px;
            font-size: 0.9em;
            flex-shrink: 0; /* Jangan menyusut */
        }
        .obat-item button:hover {
            background-color: #c82333;
        }

        /* Tombol Tambah Obat */
        .add-obat-button {
            padding: 10px 20px;
            background-color: #17a2b8; /* Cyan */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
        }
        .add-obat-button:hover {
            background-color: #138496;
            transform: translateY(-2px);
        }


        /* Bagian tombol aksi form utama */
        .form-actions {
            text-align: center;
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px; /* Jarak antar tombol */
        }

        /* Styling tombol Simpan Konsultasi */
        button[type="submit"] {
            padding: 12px 25px;
            background-color: #28a745; /* Hijau */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* Efek hover tombol Simpan */
        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* Styling link Kembali ke Daftar Konsultasi */
        .back-link {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
            padding: 12px 25px;
            border: 1px solid #3498db;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        /* Efek hover link kembali */
        .back-link:hover {
            background-color: #3498db;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.4);
        }

        /* Pesan sukses/error */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        /* Responsif untuk layar kecil */
        @media screen and (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            form div {
                flex-direction: column; /* Label dan input ditumpuk secara vertikal */
                align-items: flex-start;
            }

            label {
                margin-right: 0;
                margin-bottom: 8px;
                flex: none;
                padding-top: 0;
            }

            input[type="text"],
            input[type="number"],
            textarea,
            select {
                width: 100%;
            }

            .obat-item {
                flex-direction: column; /* Elemen obat ditumpuk */
                align-items: flex-start;
                gap: 5px;
            }

            .obat-item select,
            .obat-item input {
                width: 100%;
                margin-right: 0;
            }

            .obat-item input[type="number"] {
                width: 100px; /* Bisa tetap kecil atau 100% */
            }

            .form-actions {
                flex-direction: column;
                gap: 15px;
            }

            button[type="submit"],
            .back-link {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buat Konsultasi Baru</h2>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= $error_message; ?></div>
        <?php endif; ?>

        <form method="post">
            <div>
                <label for="id_pasien">Pasien:</label>
                <select name="id_pasien" id="id_pasien" required>
                    <option value="">Pilih Pasien</option>
                    <?php foreach ($all_pasien as $p): ?>
                        <option value="<?= $p['id_pasien']; ?>"><?= htmlspecialchars($p['nama_pasien']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_dokter">Dokter:</label>
                <select name="id_dokter" id="id_dokter" required>
                    <option value="">Pilih Dokter</option>
                    <?php foreach ($all_dokters as $dokter): ?>
                        <option value="<?= $dokter['id_dokter']; ?>"><?= htmlspecialchars($dokter['nama_dokter']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="keluhan">Keluhan:</label>
                <textarea name="keluhan" id="keluhan" rows="3" required></textarea>
            </div>
            <div>
                <label for="id_diagnosa">Diagnosa:</label>
                <select name="id_diagnosa" id="id_diagnosa">
                    <option value="">Belum Didiagnosa</option>
                    <?php foreach ($all_diagnosas as $diagnosa): ?>
                        <option value="<?= $diagnosa['id_diagnosa']; ?>"><?= htmlspecialchars($diagnosa['nama_diagnosa']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="catatan_dokter">Catatan Dokter:</label>
                <textarea name="catatan_dokter" id="catatan_dokter" rows="5"></textarea>
            </div>
        
            <h3>Obat yang Diberikan:</h3>
            <div id="obat-list">
                <div class="obat-item">
                    <select name="obat[0][id_obat]">
                        <option value="">Pilih Obat</option>
                        <?php foreach ($all_obats as $obat): ?>
                            <option value="<?= $obat['id_obat']; ?>" data-satuan="<?= htmlspecialchars($obat['satuan']); ?>"><?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['satuan']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    Jumlah: <input type="number" name="obat[0][jumlah]" value="0" min="0">
                    Instruksi: <input type="text" name="obat[0][instruksi]">
                    <button type="button" onclick="removeObat(this)">Hapus</button>
                </div>
            </div>
            <button type="button" onclick="addObat()" class="add-obat-button">Tambah Obat</button>

            <div class="form-actions">
                <button type="submit">Simpan Konsultasi</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Konsultasi</a>
            </div>
        </form>
    </div>

    <script>
        let obatCounter = 1; // Start from 1 as 0 is used for the initial item
        const allObatsOptions = `
            <?php foreach ($all_obats as $obat): ?>
                <option value="<?= $obat['id_obat']; ?>" data-satuan="<?= htmlspecialchars($obat['satuan']); ?>"><?= htmlspecialchars($obat['nama_obat']); ?> (<?= htmlspecialchars($obat['satuan']); ?>)</option>
            <?php endforeach; ?>
        `;

        function addObat() {
            const obatList = document.getElementById('obat-list');
            const newObatItem = document.createElement('div');
            newObatItem.classList.add('obat-item');
            newObatItem.innerHTML = `
                <select name="obat[${obatCounter}][id_obat]">
                    <option value="">Pilih Obat</option>
                    ${allObatsOptions}
                </select>
                Jumlah: <input type="number" name="obat[${obatCounter}][jumlah]" value="0" min="0">
                Instruksi: <input type="text" name="obat[${obatCounter}][instruksi]">
                <button type="button" onclick="removeObat(this)">Hapus</button>
            `;
            obatList.appendChild(newObatItem);
            obatCounter++;
        }

        function removeObat(button) {
            // Ensure at least one obat item remains
            const obatList = document.getElementById('obat-list');
            if (obatList.children.length > 1) {
                button.parentElement.remove();
            } else {
                alert("Minimal harus ada satu item obat.");
            }
        }
    </script>
</body>
</html>