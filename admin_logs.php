<?php
require_once "config/db.php";
require_once "config/auth.php";
require_admin();

$visits = $pdo->query("SELECT v.*, s.name, s.student_id, k.key_name FROM library_visits v JOIN students s ON s.id=v.student_id JOIN library_keys k ON k.id=v.library_key_id ORDER BY v.id DESC LIMIT 200")->fetchAll();
$page_title = "Library Logs";
include "partials/header.php";
?>
<div class="card shadow-sm p-4">
<h4>Entry / Exit Logs</h4>
<div class="table-responsive">
<table class="table table-hover">
<thead><tr><th>Student</th><th>Student ID</th><th>Key</th><th>Entry</th><th>Exit</th><th>Duration</th></tr></thead>
<tbody>
<?php foreach($visits as $v): 
$duration = "Inside";
if ($v["exit_time"]) {
    $a = new DateTime($v["entry_time"]); $b = new DateTime($v["exit_time"]);
    $duration = $a->diff($b)->format("%h h %i m");
}
?>
<tr>
<td><?= htmlspecialchars($v["name"]) ?></td><td><?= htmlspecialchars($v["student_id"]) ?></td>
<td><?= htmlspecialchars($v["key_name"]) ?></td><td><?= $v["entry_time"] ?></td>
<td><?= $v["exit_time"] ?: '<span class="badge bg-warning text-dark">Still inside</span>' ?></td><td><?= $duration ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
<?php include "partials/footer.php"; ?>
