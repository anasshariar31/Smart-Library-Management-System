<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? "Smart Library") ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Smart Library</a>
        <?php if (isset($_SESSION["user"])): ?>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="scan.php">QR Scan</a></li>
                <li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
                <?php if ($_SESSION["user"]["role"] === "admin"): ?>
                    <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_logs.php">Logs</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="logout.php">Logout</a></li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container py-4">
