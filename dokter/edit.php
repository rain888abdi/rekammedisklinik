<?php
// Pastikan tidak ada spasi, baris kosong, atau karakter lain sebelum tag ini.
// Baris ini harus menjadi baris pertama dan tidak ada yang lain sebelumnya.
include '../db.php';

$id_dokter = isset($_GET['id']) ? intval($_GET['id']) : 0;
$dokter = null; // Inisialisasi variabel dokter

// Jika ID dokter tidak valid, arahkan kembali
if ($id_dokter === 0) {
    header("Location: index.php");
    exit;
}

// Proses form jika ada data POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_dokter = $_POST['nama_dokter'];
    $spesialisasi = $_POST['spesialisasi'];
    $telepon = $_POST['telepon'];

    $stmt = $conn->prepare("UPDATE Dokter SET nama_dokter=?, spesialisasi=?, telepon=? WHERE id_dokter=?");
    $stmt->bind_param("sssi", $nama_dokter, $spesialisasi, $telepon, $id_dokter);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit(); // Penting: Hentikan eksekusi script setelah redirect
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

// Ambil data dokter untuk mengisi form (dilakukan setelah POST agar data yang diperbarui bisa langsung diambil jika ada error)
$stmt_dokter = $conn->prepare("SELECT * FROM Dokter WHERE id_dokter = ?");
$stmt_dokter->bind_param("i", $id_dokter);
$stmt_dokter->execute();
$result_dokter = $stmt_dokter->get_result();
$dokter = $result_dokter->fetch_assoc();
$stmt_dokter->close();

// Jika dokter tidak ditemukan setelah pengambilan data
if (!$dokter) {
    echo "Dokter tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Dokter</title>
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
        input[type="tel"] {
            flex: 1; /* Input field mengambil sisa ruang yang tersedia */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Efek fokus pada input */
        input[type="text"]:focus,
        input[type="tel"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Bagian tombol aksi form */
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }

        /* Styling tombol Update Dokter */
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

        /* Styling link Kembali ke Daftar Dokter */
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
            input[type="tel"] {
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
        <h2>Edit Dokter</h2>
        <?php
        // Tampilkan pesan error jika ada
        if (isset($error_message)) {
            echo "<p style='color: red; text-align: center; margin-top: 20px;'>" . $error_message . "</p>";
        }
        ?>
        <form method="post">
            <div>
                <label for="nama_dokter">Nama Dokter:</label>
                <input type="text" name="nama_dokter" id="nama_dokter" value="<?= htmlspecialchars($dokter['nama_dokter']); ?>" required>
            </div>
            <div>
                <label for="spesialisasi">Spesialisasi:</label>
                <input type="text" name="spesialisasi" id="spesialisasi" value="<?= htmlspecialchars($dokter['spesialisasi']); ?>">
            </div>
            <div>
                <label for="telepon">Telepon:</label>
                <input type="tel" name="telepon" id="telepon" value="<?= htmlspecialchars($dokter['telepon']); ?>">
            </div>
            <div class="form-actions">
                <button type="submit">Update Dokter</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Dokter</a>
            </div>
        </form>
    </div>
</body>
</html>