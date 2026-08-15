<?php
require_once "config/db.php";
require_once "config/auth.php";
require_login();

$students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$books = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$available = $pdo->query("SELECT COUNT(*) FROM books WHERE available_copies > 0")->fetchColumn();
$active_loans = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='issued'")->fetchColumn();
$today_entries = $pdo->query("SELECT COUNT(*) FROM library_visits WHERE DATE(entry_time)=CURDATE()")->fetchColumn();

$page_title = "Dashboard";
include "partials/header.php";
?>
<div class="hero p-4 p-md-5 mb-4">
    <h1>Welcome, <?= htmlspecialchars($_SESSION["user"]["name"]) ?> 👋</h1>
    <p class="mb-0">Manage students, QR attendance, books and library records from one place.</p>
</div>
<div class="row g-3">
<?php
$stats = [
 ["Students",$students,"students.php"],
 ["Total Books",$books,"books.php"],
 ["Available Books",$available,"books.php"],
 ["Active Loans",$active_loans,"books.php"],
 ["Today's Visits",$today_entries,"admin_logs.php"]
];
foreach($stats as $s): ?>
<div class="col-sm-6 col-lg-3">
    <a class="text-decoration-none text-dark" href="<?= $s[2] ?>">
    <div class="card shadow-sm p-4 stat-card h-100">
        <div class="text-muted"><?= $s[0] ?></div>
        <div class="display-6 fw-bold text-primary"><?= $s[1] ?></div>
    </div>
    </a>
</div>
<?php endforeach; ?>
</div>
<div class="row g-3 mt-2">
    <div class="col-md-6">
        <div class="card shadow-sm p-4">
            <h5>Quick Actions</h5>
            <div class="d-grid gap-2 mt-2">
                <a href="scan.php" class="btn btn-primary">📷 Scan Student / Library Key</a>
                <a href="books.php" class="btn btn-outline-primary">📖 Issue / Return Book</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm p-4">
            <h5>System Features</h5>
            <ul class="mb-0">
                <li>QR-based student identification</li>
                <li>Library key entry/exit tracking</li>
                <li>Book issue and return records</li>
                <li>Due-date and overdue status</li>
            </ul>
        </div>
    </div>
</div>
<?php include "partials/footer.php"; ?>
