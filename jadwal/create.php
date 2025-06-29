<?php
// Pastikan tidak ada spasi, baris kosong, atau karakter lain sebelum tag ini.
// Baris ini harus menjadi baris pertama dan tidak ada yang lain sebelumnya.
include '../db.php';

// Cek jika request method adalah POST (form disubmit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $id_dokter = $_POST['id_dokter'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO Jadwal (id_dokter, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_dokter, $hari, $jam_mulai, $jam_selesai);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect ke index.php di folder yang sama
        exit(); // Penting: Hentikan eksekusi script setelah redirect
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

// Ambil daftar dokter untuk dropdown
$dokters = $conn->query("SELECT id_dokter, nama_dokter FROM Dokter ORDER BY nama_dokter ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Jadwal Dokter</title>
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

        /* Styling untuk input time dan select */
        input[type="time"],
        select {
            flex: 1; /* Input field mengambil sisa ruang yang tersedia */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            appearance: none; /* Menghilangkan styling default browser pada select */
            background-color: #fff;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007bff%22%20d%3D%22M287%2C146.206L146.2%2C0L5.4%2C146.206h281.6z%22%2F%3E%3C%2Fsvg%3E'); /* Custom arrow untuk select */
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px;
            cursor: pointer;
        }

        /* Efek fokus pada input dan select */
        input[type="time"]:focus,
        select:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Bagian tombol aksi form */
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }

        /* Styling tombol Simpan Jadwal */
        button[type="submit"] {
            padding: 12px 25px;
            background-color: #28a745; /* Warna hijau standar untuk simpan */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-right: 15px;
        }

        /* Efek hover tombol Simpan */
        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* Styling link Kembali ke Daftar Jadwal */
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

            input[type="time"],
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
        <h2>Tambah Jadwal Dokter Baru</h2>
        <?php
        // Tampilkan pesan error jika ada
        if (isset($error_message)) {
            echo "<p style='color: red; text-align: center; margin-top: 20px;'>" . $error_message . "</p>";
        }
        ?>
        <form method="post">
            <div>
                <label for="id_dokter">Dokter:</label>
                <select name="id_dokter" id="id_dokter" required>
                    <option value="">Pilih Dokter</option>
                    <?php foreach ($dokters as $dokter): ?>
                        <option value="<?= $dokter['id_dokter']; ?>"><?= htmlspecialchars($dokter['nama_dokter']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="hari">Hari:</label>
                <select name="hari" id="hari" required>
                    <option value="">Pilih Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
            </div>
            <div>
                <label for="jam_mulai">Jam Mulai:</label>
                <input type="time" name="jam_mulai" id="jam_mulai" required>
            </div>
            <div>
                <label for="jam_selesai">Jam Selesai:</label>
                <input type="time" name="jam_selesai" id="jam_selesai" required>
            </div>
            <div class="form-actions">
                <button type="submit">Simpan Jadwal</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Jadwal</a>
            </div>
        </form>
    </div>
</body>
</html>