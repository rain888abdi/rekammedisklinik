<?php
// Pastikan tidak ada spasi, baris kosong, atau karakter lain sebelum tag ini.
// Baris ini harus menjadi baris pertama dan tidak ada yang lain sebelumnya.
include '../db.php';

$error_message = ''; // Inisialisasi pesan error

// Cek jika request method adalah POST (form disubmit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $nama_diagnosa = $_POST['nama_diagnosa'];
    $deskripsi_diagnosa = $_POST['deskripsi_diagnosa'];

    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO Diagnosa (nama_diagnosa, deskripsi_diagnosa) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_diagnosa, $deskripsi_diagnosa);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect ke index.php di folder yang sama
        exit(); // Penting: Hentikan eksekusi script setelah redirect
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Diagnosa Baru</title>
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
            align-items: flex-start; /* Menyelaraskan label dan input secara vertikal (untuk textarea) */
        }

        /* Styling label */
        label {
            flex: 0 0 150px; /* Lebar tetap untuk label */
            margin-right: 20px;
            font-weight: 600;
            color: #555;
            padding-top: 10px; /* Agar sejajar dengan bagian atas input/textarea */
        }

        /* Styling untuk input text dan textarea */
        input[type="text"],
        textarea {
            flex: 1; /* Input field mengambil sisa ruang yang tersedia */
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
        }

        textarea {
            resize: vertical; /* Hanya memungkinkan resize vertikal */
            min-height: 100px; /* Tinggi minimum untuk textarea */
        }

        /* Efek fokus pada input dan textarea */
        input[type="text"]:focus,
        textarea:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Bagian tombol aksi form */
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }

        /* Styling tombol Simpan Diagnosa */
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

        /* Styling link Kembali ke Daftar Diagnosa */
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
                padding-top: 0; /* Reset padding-top untuk responsif */
            }

            input[type="text"],
            textarea {
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
        <h2>Tambah Diagnosa Baru</h2>
        <?php
        // Tampilkan pesan error jika ada
        if (!empty($error_message)) {
            echo "<p style='color: red; text-align: center; margin-top: 20px;'>" . $error_message . "</p>";
        }
        ?>
        <form method="post">
            <div>
                <label for="nama_diagnosa">Nama Diagnosa:</label>
                <input type="text" name="nama_diagnosa" id="nama_diagnosa" required>
            </div>
            <div>
                <label for="deskripsi_diagnosa">Deskripsi:</label>
                <textarea name="deskripsi_diagnosa" id="deskripsi_diagnosa" rows="5"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit">Simpan Diagnosa</button>
                <a href="index.php" class="back-link">Kembali ke Daftar Diagnosa</a>
            </div>
        </form>
    </div>
</body>
</html>