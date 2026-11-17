<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include("config.php");

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    if ($type === 'found') {
        $stmt = $conn->prepare("DELETE FROM found_items WHERE id = ?");
    } elseif ($type === 'missing') {
        $stmt = $conn->prepare("DELETE FROM missing_items WHERE id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: admin_reports.php");
exit;
?>
