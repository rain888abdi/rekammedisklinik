<?php include '../db.php'; // Perhatikan path relatif ?>

<?php
if (isset($_GET['id'])) {
    $id_dokter = intval($_GET['id']);

    // Hapus konsultasi terkait dokter ini (jika ada, asumsi ON DELETE CASCADE atau manual)
    // Sebaiknya database sudah diatur dengan ON DELETE CASCADE pada foreign key id_dokter di tabel Konsultasi
    // Jika tidak, Anda perlu menghapus data di Konsultasi terlebih dahulu atau menanggulangi error foreign key constraint.
    $stmt_konsultasi = $conn->prepare("DELETE FROM Konsultasi WHERE id_dokter=?");
    $stmt_konsultasi->bind_param("i", $id_dokter);
    $stmt_konsultasi->execute();
    $stmt_konsultasi->close();

    // Hapus dokter
    $stmt_dokter = $conn->prepare("DELETE FROM Dokter WHERE id_dokter=?");
    $stmt_dokter->bind_param("i", $id_dokter);
    
    if ($stmt_dokter->execute()) {
        header("Location: index.php"); // Redirect ke index.php di folder yang sama
    } else {
        echo "Error: " . $stmt_dokter->error;
    }
    $stmt_dokter->close();
} else {
    header("Location: index.php"); // Redirect jika ID tidak ditemukan
}
?>