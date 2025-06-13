<?php include '../db.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id_konsultasi = intval($_GET['id']);

    // Hapus detail obat terkait konsultasi ini terlebih dahulu
    $stmt_detail_obat = $conn->prepare("DELETE FROM DetailKonsultasiObat WHERE id_konsultasi=?");
    $stmt_detail_obat->bind_param("i", $id_konsultasi);
    $stmt_detail_obat->execute();
    $stmt_detail_obat->close();

    // Hapus konsultasi
    $stmt_konsultasi = $conn->prepare("DELETE FROM Konsultasi WHERE id_konsultasi=?");
    $stmt_konsultasi->bind_param("i", $id_konsultasi);
    
    if ($stmt_konsultasi->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt_konsultasi->error;
    }
    $stmt_konsultasi->close();
} else {
    header("Location: index.php");
}
?>