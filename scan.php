<?php
require_once "config/db.php";
require_once "config/auth.php";
require_login();

$message = "";
$error = "";
$student = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_qr = trim($_POST["student_qr"] ?? "");
    $key_qr = trim($_POST["key_qr"] ?? "");

    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ? OR qr_code = ?");
    $stmt->execute([$student_qr, $student_qr]);
    $student = $stmt->fetch();

    if (!$student) {
        $error = "Student not found. Scan a valid university ID QR code.";
    } elseif ($key_qr === "") {
        $error = "Please scan the library key QR code.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM library_keys WHERE qr_code = ? AND status='active'");
        $stmt->execute([$key_qr]);
        $key = $stmt->fetch();

        if (!$key) {
            $error = "Invalid or inactive library key QR code.";
        } else {
            $open = $pdo->prepare("SELECT * FROM library_visits WHERE student_id=? AND exit_time IS NULL ORDER BY id DESC LIMIT 1");
            $open->execute([$student["id"]]);
            $visit = $open->fetch();

            if ($visit) {
                $stmt = $pdo->prepare("UPDATE library_visits SET exit_time=NOW(), library_key_id=? WHERE id=?");
                $stmt->execute([$key["id"], $visit["id"]]);
                $message = "Exit recorded successfully for " . $student["name"] . ".";
            } else {
                $stmt = $pdo->prepare("INSERT INTO library_visits(student_id, library_key_id, entry_time) VALUES(?,?,NOW())");
                $stmt->execute([$student["id"], $key["id"]]);
                $message = "Entry recorded successfully for " . $student["name"] . ".";
            }
        }
    }
}
$page_title = "QR Scan";
include "partials/header.php";
?>
<div id="alertBox"></div>
<div class="row g-4">
<div class="col-lg-6">
<div class="card shadow-sm p-4">
    <h4>📷 Scan Student ID</h4>
    <p class="text-muted">The QR value should match the student's Student ID or stored QR code.</p>
    <div id="student-reader" class="qr-box"></div>
    <input id="student_qr" class="form-control mt-3" placeholder="Scanned Student ID / QR value">
</div>
</div>
<div class="col-lg-6">
<div class="card shadow-sm p-4">
    <h4>🔑 Scan Library Key</h4>
    <div id="key-reader" class="qr-box"></div>
    <input id="key_qr" class="form-control mt-3" placeholder="Scanned Library Key QR">
</div>
</div>
</div>

<div class="card shadow-sm p-4 mt-4">
    <form method="post" id="scanForm">
        <input type="hidden" name="student_qr" id="studentHidden">
        <input type="hidden" name="key_qr" id="keyHidden">
        <button class="btn btn-primary" type="submit">Record Entry / Exit</button>
    </form>
</div>

<?php if ($message): ?><div class="alert alert-success mt-3"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
function onScanStudent(decodedText) {
    document.getElementById('student_qr').value = decodedText;
    document.getElementById('studentHidden').value = decodedText;
}
function onScanKey(decodedText) {
    document.getElementById('key_qr').value = decodedText;
    document.getElementById('keyHidden').value = decodedText;
}
new Html5QrcodeScanner("student-reader", {fps:10, qrbox:220}, false).render(onScanStudent, ()=>{});
new Html5QrcodeScanner("key-reader", {fps:10, qrbox:220}, false).render(onScanKey, ()=>{});
document.getElementById('student_qr').addEventListener('input', e => document.getElementById('studentHidden').value=e.target.value);
document.getElementById('key_qr').addEventListener('input', e => document.getElementById('keyHidden').value=e.target.value);
</script>
<?php include "partials/footer.php"; ?>
