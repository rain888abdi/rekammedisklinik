<?php include '../db.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id_pasien = intval($_GET['id']);

    // Hapus konsultasi terkait pasien ini terlebih dahulu
    // Ini penting jika Konsultasi memiliki foreign key constraint ON DELETE RESTRICT
    // Jika Anda memiliki ON DELETE CASCADE pada foreign key id_pasien di Konsultasi, baris ini tidak wajib
    // Tapi secara umum, menghapus secara manual/bertahap lebih aman.
    $stmt_delete_konsultasi = $conn->prepare("DELETE FROM Konsultasi WHERE id_pasien = ?");
    $stmt_delete_konsultasi->bind_param("i", $id_pasien);
    $stmt_delete_konsultasi->execute();
    $stmt_delete_konsultasi->close();

    // Hapus pasien
    $stmt_pasien = $conn->prepare("DELETE FROM Pasien WHERE id_pasien=?");
    $stmt_pasien->bind_param("i", $id_pasien);

    if ($stmt_pasien->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt_pasien->error;
    }
    $stmt_pasien->close();
} else {
    header("Location: index.php");
}
?>