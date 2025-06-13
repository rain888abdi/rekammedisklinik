<?php
// Aktifkan error reporting untuk debugging selama pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php'; // Pastikan file ini bersih dari spasi atau output sebelum <?php

// Inisialisasi variabel bulan dan tahun
$bulan_sekarang = date('n');
$tahun_sekarang = date('Y');

// Definisikan nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Ambil bulan dan tahun dari GET request jika ada
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : $bulan_sekarang;
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : $tahun_sekarang;

// Pastikan bulan dan tahun berada dalam rentang yang valid
if ($bulan < 1 || $bulan > 12) {
    $bulan = $bulan_sekarang;
}
if ($tahun < 2000 || $tahun > $tahun_sekarang + 5) {
    $tahun = $tahun_sekarang;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Jumlah Pasien per Dokter</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4CAF50; /* Hijau */
            --primary-dark: #45a049;
            --secondary-color: #007bff; /* Biru */
            --secondary-dark: #0056b3;
            --text-color: #333;
            --bg-color: #f8f9fa;
            --card-bg: #fff;
            --border-color: #e0e0e0;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            --table-header-bg: #e9ecef;
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
            max-width: 900px;
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

        .link-back {
            display: block;
            text-align: center;
            margin-bottom: 25px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .link-back:hover {
            color: var(--secondary-dark);
            text-decoration: underline;
        }

        .filter-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .filter-form label {
            font-weight: 500;
            color: #555;
        }

        .filter-form select,
        .filter-form input[type="number"] {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-form select:focus,
        .filter-form input[type="number"]:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: var(--shadow);
        }
        .filter-form button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        h3 {
            text-align: center;
            color: var(--secondary-color);
            margin-top: 25px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: separate; /* Use separate for rounded corners */
            border-spacing: 0;
            margin-top: 20px;
            box-shadow: var(--shadow);
            border-radius: 10px; /* Apply border-radius to the table itself */
            overflow: hidden; /* Hide overflowing content like border-radius corners */
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--table-header-bg);
            color: var(--text-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none; /* No border at the very bottom of the table */
        }

        tr:nth-child(even) {
            background-color: #f6f6f6;
        }

        tr:hover {
            background-color: #eaf7ff;
            cursor: pointer;
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #666;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        /* Responsiveness */
        @media (max-width: 600px) {
            .container {
                margin: 20px 10px;
                padding: 20px;
            }
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-form select,
            .filter-form input[type="number"],
            .filter-form button {
                width: 100%;
                margin-bottom: 10px; /* Add space between stacked elements */
            }
            .filter-form button {
                margin-bottom: 0;
            }
            table, th, td {
                display: block; /* Make table elements stack */
                width: 100%;
            }
            th {
                text-align: right;
                padding-top: 10px;
                padding-bottom: 0;
            }
            td {
                text-align: right;
                padding-top: 5px;
                padding-bottom: 10px;
                position: relative;
                padding-left: 50%; /* Space for pseudo-element label */
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: calc(50% - 30px);
                text-align: left;
                font-weight: 600;
                color: #555;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Rekap Jumlah Pasien per Dokter</h2>
        <a href="../index.php" class="link-back">Kembali ke Beranda</a>

        <form method="get" class="filter-form">
            <label for="bulan">Bulan:</label>
            <select name="bulan" id="bulan" required>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    echo '<option value="' . $i . '"' . (($i == $bulan) ? 'selected' : '') . '>' . $nama_bulan[$i] . '</option>';
                }
                ?>
            </select>
            <label for="tahun">Tahun:</label>
            <input type="number" name="tahun" id="tahun" value="<?= htmlspecialchars($tahun); ?>" min="2000" max="<?= date('Y') + 5; ?>" required>
            <button type="submit">Tampilkan Rekap</button>
        </form>

        <?php
        // Data rekap akan ditampilkan berdasarkan bulan dan tahun yang dipilih
        // Ini adalah bagian yang sama dengan kode PHP Anda sebelumnya, tetapi dengan styling baru
        echo "<h3>Rekap Pasien Bulan " . htmlspecialchars($nama_bulan[$bulan]) . " Tahun " . htmlspecialchars($tahun) . "</h3>";

        // Memanggil Stored Procedure
        $stmt = $conn->prepare("CALL GetRekapPasienPerDokter(?, ?)");
        if ($stmt === false) {
            echo "<p class='no-data-message' style='color: red;'>Error menyiapkan statement: " . htmlspecialchars($conn->error) . "</p>";
        } else {
            $stmt->bind_param("ii", $bulan, $tahun);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr><th>Nama Dokter</th><th>Jumlah Pasien</th></tr></thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td data-label='Nama Dokter'>" . htmlspecialchars($row['nama_dokter']) . "</td>";
                    echo "<td data-label='Jumlah Pasien'>" . htmlspecialchars($row['total_pasien']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='no-data-message'>Tidak ada data konsultasi untuk bulan dan tahun yang dipilih.</p>";
            }
            $stmt->close();
        }
        ?>
    </div>
</body>
</html>