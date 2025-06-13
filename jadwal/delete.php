<?php include '../db.php'; ?>
<?php
if (isset($_GET['id'])) {
    $id_jadwal = intval($_GET['id']);

    $stmt_jadwal = $conn->prepare("DELETE FROM Jadwal WHERE id_jadwal=?");
    $stmt_jadwal->bind_param("i", $id_jadwal);
    
    if ($stmt_jadwal->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt_jadwal->error;
    }
    $stmt_jadwal->close();
} else {
    header("Location: index.php");
}
?>