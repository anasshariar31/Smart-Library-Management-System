<?php
require_once "config/db.php";
require_once "config/auth.php";
require_admin();

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $student_id = trim($_POST["student_id"]);
    $batch = trim($_POST["batch"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $qr = trim($_POST["qr_code"]) ?: $student_id;

    try {
        $stmt = $pdo->prepare("INSERT INTO students(name,student_id,batch,phone,email,qr_code) VALUES(?,?,?,?,?,?)");
        $stmt->execute([$name,$student_id,$batch,$phone,$email,$qr]);
        $message = "Student added successfully.";
    } catch (PDOException $e) {
        $message = "Could not add student. Student ID or QR code may already exist.";
    }
}
$rows = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
$page_title = "Students";
include "partials/header.php";
?>
<div class="card shadow-sm p-4 mb-4">
<h4>Add Student</h4>
<?php if($message): ?><div class="alert alert-info"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<form method="post" class="row g-3">
<div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Student ID</label><input name="student_id" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Batch</label><input name="batch" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
<div class="col-md-8"><label class="form-label">QR Code Value</label><input name="qr_code" class="form-control" placeholder="Leave empty to use Student ID"></div>
<div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">Add Student</button></div>
</form>
</div>
<div class="card shadow-sm p-4">
<h4>Student Records</h4>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Name</th><th>ID</th><th>Batch</th><th>Phone</th><th>QR Value</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?><tr>
<td><?= htmlspecialchars($r["name"]) ?></td><td><?= htmlspecialchars($r["student_id"]) ?></td>
<td><?= htmlspecialchars($r["batch"]) ?></td><td><?= htmlspecialchars($r["phone"]) ?></td>
<td><span class="badge bg-light text-dark"><?= htmlspecialchars($r["qr_code"]) ?></span></td>
</tr><?php endforeach; ?>
</tbody></table></div>
</div>
<?php include "partials/footer.php"; ?>
