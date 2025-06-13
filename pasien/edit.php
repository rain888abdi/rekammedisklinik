<?php
// Pastikan tidak ada spasi, baris kosong, atau karakter lain sebelum tag ini.
// Baris ini harus menjadi baris pertama dan tidak ada yang lain sebelumnya.
include '../db.php';

$id_pasien = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pasien = null; // Inisialisasi variabel pasien

// Jika ID pasien tidak valid, arahkan kembali
if ($id_pasien === 0) {
    header("Location: index.php");
    exit;
}

// Proses form jika ada data POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pasien = $_POST['nama_pasien'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];

    $stmt = $conn->prepare("UPDATE Pasien SET nama_pasien=?, tanggal_lahir=?, jenis_kelamin=?, alamat=?, telepon=? WHERE id_pasien=?");
    $stmt->bind_param("sssssi", $nama_pasien, $tanggal_lahir, $jenis_kelamin, $alamat, $telepon, $id_pasien);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit(); // Penting: Hentikan eksekusi script setelah redirect
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

// Ambil data pasien untuk mengisi form (dilakukan setelah POST agar data yang diperbarui bisa langsung diambil jika ada error)
$stmt_pasien = $conn->prepare("SELECT * FROM Pasien WHERE id_pasien = ?");
$stmt_pasien->bind_param("i", $id_pasien);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();
$pasien = $result_pasien->fetch_assoc();
$stmt_pasien->close();

// Jika pasien tidak ditemukan setelah pengambilan data
if (!$pasien) {
    echo "Pasien tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pasien</title>
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
            min-height: 100vh; /* Memastikan form di tengah secara vertikal pada layar besar */
        }

        /* Container utama form */
        .container {
            width: 90%;
            max-width: 600px; /* Lebar maksimal disesuaikan untuk form */
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        /* Setiap baris form (label + input) */
        form div {
            margin-bottom: 18px;
            display: flex; /* Menggunakan flexbox untuk penataan label-input */
            align-items: center; /* Menyelaraskan label dan input secara vertikal */
        }

        /* Styling label */
        label {
            flex: 0 0 150px; /* Lebar tetap untuk label */
            margin-right: 20px;
            font-weight: 600;
            color: #555;
        }

        /* Styling untuk semua jenis input teks, tanggal, telepon, textarea, dan select */
        input[type="text"],
        input[type="date"],
        input[type="tel"],
        textarea,
        select {
            flex: 1; /* Input field mengambil sisa ruang yang tersedia */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Efek fokus pada input */
        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus,
        textarea:focus,
        select:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Styling khusus textarea */
        textarea {
            resize: vertical; /* Memungkinkan textarea diubah ukurannya secara vertikal */
            min-height: 80px; /* Tinggi minimal textarea */
        }

        /* Bagian tombol aksi form */
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }

        /* Styling tombol Update Pasien */
        button[type="submit"] {
            padding: 12px 25px;
            background-color: #007bff; /* Warna biru standar untuk update */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-right: 15px;
        }

        /* Efek hover tombol Update */
        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Styling link Kembali ke Daftar Pasien */
        .back-link {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
            padding: 12px 25px;
            border: 1px solid #3498db;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block; /* Untuk menerapkan padding dan margin dengan benar */
        }

        /* Efek hover link kembali */
        .back-link:hover {
            background-color: #3498db;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.4);
        }

        /* Penyesuaian responsif untuk layar kecil */
        @media screen and (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            form div {
                flex-direction: column; /* Label dan input ditumpuk secara vertikal */
                align-items: flex-start; /* Label sejajar ke kiri */
            }

            label {
                margin-right: 0;
                margin-bottom: 8px; /* Jarak antara label dan input */
                flex: none; /* Hapus lebar tetap flex */
            }

            input[type="text"],
            input[type="date"],
            input[type="tel"],
            textarea,
            select {
                width: 100%; /* Input mengambil lebar penuh */
            }

            .form-actions {
                flex-direction: column; /* Tombol ditumpuk secara vertikal */
                align-items: center;
            }

            button[type="submit"],
            .back-link {
                width: 100%;
                margin-right: 0;
                margin-bottom: 15px; /* Jarak antar tombol yang ditumpuk */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Pasien</h2>
        <?php
        // Tampilkan pesan error jika ada
        if (isset($error_message)) {
            echo "<p style='color: red; text-align: center; margin-top: 20px;'>" . $error_message . "</p>";
        }
        ?>
        <form method="post">
            <div>
                <label for="nama_pasien">Nama Pasien:</label>
                <input type="text" name="nama_pasien" id="nama_pasien" value="<?= htmlspecialchars($pasien['nama_pasien']); ?>" required>
            </div>
            <div>
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="<?= htmlspecialchars($pasien['tanggal_lahir']); ?>">
            </div>
            <div>
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select name="jenis_kelamin" id="jenis_kelamin" required>
                    <option value="Laki-laki" <?= ($pasien['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?= ($pasien['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label for="alamat">Alamat:</label>
                <textarea name="alamat" id="alamat" rows="3"><?= htmlspecialchars($pasien['alamat']); ?></textarea>
            </div>
            <div>
                <label for="telepon">Telepon:</label>
                <input type="tel" name="telepon" id="telepon" value="<?= htmlspecialchars($pasien['telepon']); ?>">
            </div>
            <div class="form-actions">
                <button type="submit">Update Pasien</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Pasien</a>
            </div>
        </form>
    </div>
</body>
</html>