<?php include '../db.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id_diagnosa = intval($_GET['id']);

    // Karena id_diagnosa di tabel Konsultasi adalah foreign key yang nullable,
    // kita bisa mengatur NULL pada Konsultasi sebelum menghapus Diagnosa.
    // Atau jika sudah diatur ON DELETE SET NULL pada foreign key constraint, ini otomatis.
    $stmt_update_konsultasi = $conn->prepare("UPDATE Konsultasi SET id_diagnosa = NULL WHERE id_diagnosa = ?");
    $stmt_update_konsultasi->bind_param("i", $id_diagnosa);
    $stmt_update_konsultasi->execute();
    $stmt_update_konsultasi->close();

    // Hapus diagnosa
    $stmt_diagnosa = $conn->prepare("DELETE FROM Diagnosa WHERE id_diagnosa=?");
    $stmt_diagnosa->bind_param("i", $id_diagnosa);
    
    if ($stmt_diagnosa->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt_diagnosa->error;
    }
    $stmt_diagnosa->close();
} else {
    header("Location: index.php");
}
?>