<?php include '../db.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id_obat = intval($_GET['id']);

    // Hapus relasi di DetailKonsultasiObat terlebih dahulu
    $stmt_detail_obat = $conn->prepare("DELETE FROM DetailKonsultasiObat WHERE id_obat=?");
    $stmt_detail_obat->bind_param("i", $id_obat);
    $stmt_detail_obat->execute();
    $stmt_detail_obat->close();

    // Hapus obat
    $stmt_obat = $conn->prepare("DELETE FROM Obat WHERE id_obat=?");
    $stmt_obat->bind_param("i", $id_obat);
    
    if ($stmt_obat->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt_obat->error;
    }
    $stmt_obat->close();
} else {
    header("Location: index.php");
}
?>