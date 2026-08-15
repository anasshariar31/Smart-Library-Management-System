<?php
require_once "config/db.php";
require_once "config/auth.php";
require_login();

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    if ($action === "add" && $_SESSION["user"]["role"] === "admin") {
        $title = trim($_POST["title"]);
        $author = trim($_POST["author"]);
        $isbn = trim($_POST["isbn"]);
        $copies = max(1, (int)$_POST["copies"]);
        $stmt = $pdo->prepare("INSERT INTO books(title,author,isbn,total_copies,available_copies) VALUES(?,?,?,?,?)");
        $stmt->execute([$title,$author,$isbn,$copies,$copies]);
        $message = "Book added.";
    }
    if ($action === "issue") {
        $student_id = trim($_POST["student_id"]);
        $book_id = (int)$_POST["book_id"];
        $days = max(1, min(60, (int)$_POST["days"]));
        $s = $pdo->prepare("SELECT id FROM students WHERE student_id=?");
        $s->execute([$student_id]); $student = $s->fetch();
        $b = $pdo->prepare("SELECT * FROM books WHERE id=? AND available_copies>0");
        $b->execute([$book_id]); $book = $b->fetch();
        if (!$student || !$book) $message = "Invalid student or unavailable book.";
        else {
            $stmt = $pdo->prepare("INSERT INTO loans(student_id,book_id,issue_date,due_date,status) VALUES(?,?,CURDATE(),DATE_ADD(CURDATE(), INTERVAL ? DAY),'issued')");
            $stmt->execute([$student["id"],$book_id,$days]);
            $pdo->prepare("UPDATE books SET available_copies=available_copies-1 WHERE id=?")->execute([$book_id]);
            $message = "Book issued successfully.";
        }
    }
    if ($action === "return") {
        $loan_id = (int)$_POST["loan_id"];
        $stmt = $pdo->prepare("SELECT * FROM loans WHERE id=? AND status='issued'");
        $stmt->execute([$loan_id]); $loan = $stmt->fetch();
        if ($loan) {
            $pdo->prepare("UPDATE loans SET return_date=CURDATE(), status='returned' WHERE id=?")->execute([$loan_id]);
            $pdo->prepare("UPDATE books SET available_copies=available_copies+1 WHERE id=?")->execute([$loan["book_id"]]);
            $message = "Book returned successfully.";
        }
    }
}
$books = $pdo->query("SELECT * FROM books ORDER BY title")->fetchAll();
$loans = $pdo->query("SELECT l.*, s.name, s.student_id, b.title FROM loans l JOIN students s ON s.id=l.student_id JOIN books b ON b.id=l.book_id ORDER BY l.id DESC")->fetchAll();
$page_title = "Books";
include "partials/header.php";
?>
<?php if($message): ?><div class="alert alert-info"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<?php if ($_SESSION["user"]["role"] === "admin"): ?>
<div class="card shadow-sm p-4 mb-4">
<h4>Add Book</h4>
<form method="post" class="row g-3">
<input type="hidden" name="action" value="add">
<div class="col-md-4"><input name="title" class="form-control" placeholder="Book title" required></div>
<div class="col-md-3"><input name="author" class="form-control" placeholder="Author" required></div>
<div class="col-md-3"><input name="isbn" class="form-control" placeholder="ISBN"></div>
<div class="col-md-2"><input name="copies" type="number" min="1" value="1" class="form-control"></div>
<div class="col-12"><button class="btn btn-primary">Add Book</button></div>
</form>
</div>
<?php endif; ?>

<div class="row g-4">
<div class="col-lg-5">
<div class="card shadow-sm p-4">
<h4>Issue Book</h4>
<form method="post">
<input type="hidden" name="action" value="issue">
<label class="form-label">Student ID</label><input name="student_id" class="form-control mb-3" required>
<label class="form-label">Book</label>
<select name="book_id" class="form-select mb-3" required>
<option value="">Select book</option>
<?php foreach($books as $b): if($b["available_copies"]>0): ?>
<option value="<?= $b["id"] ?>"><?= htmlspecialchars($b["title"]) ?> (<?= $b["available_copies"] ?> available)</option>
<?php endif; endforeach; ?>
</select>
<label class="form-label">Loan days</label><input name="days" type="number" min="1" max="60" value="14" class="form-control mb-3">
<button class="btn btn-primary w-100">Issue Book</button>
</form>
</div>
</div>
<div class="col-lg-7">
<div class="card shadow-sm p-4">
<h4>Book Catalog</h4>
<div class="table-responsive"><table class="table">
<tr><th>Title</th><th>Author</th><th>Available</th></tr>
<?php foreach($books as $b): ?><tr>
<td><?= htmlspecialchars($b["title"]) ?></td><td><?= htmlspecialchars($b["author"]) ?></td><td><?= $b["available_copies"] ?>/<?= $b["total_copies"] ?></td>
</tr><?php endforeach; ?>
</table></div>
</div>
</div>
</div>

<div class="card shadow-sm p-4 mt-4">
<h4>Loan Records & Returns</h4>
<div class="table-responsive"><table class="table table-hover">
<tr><th>Student</th><th>Book</th><th>Issue</th><th>Due</th><th>Status</th><th>Action</th></tr>
<?php foreach($loans as $l): ?>
<tr>
<td><?= htmlspecialchars($l["name"]) ?><br><small><?= htmlspecialchars($l["student_id"]) ?></small></td>
<td><?= htmlspecialchars($l["title"]) ?></td><td><?= $l["issue_date"] ?></td><td><?= $l["due_date"] ?></td>
<td><?= $l["status"] === "issued" && $l["due_date"] < date("Y-m-d") ? '<span class="badge bg-danger">Overdue</span>' : '<span class="badge bg-success">'.htmlspecialchars($l["status"]).'</span>' ?></td>
<td><?php if($l["status"]==="issued"): ?><form method="post"><input type="hidden" name="action" value="return"><input type="hidden" name="loan_id" value="<?= $l["id"] ?>"><button class="btn btn-sm btn-outline-success">Return</button></form><?php else: ?>—<?php endif; ?></td>
</tr>
<?php endforeach; ?>
</table></div>
</div>
<?php include "partials/footer.php"; ?>
