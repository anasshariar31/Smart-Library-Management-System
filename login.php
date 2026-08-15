<?php
require_once "config/db.php";
require_once "config/auth.php";

if (isset($_SESSION["user"])) {
    header("Location: dashboard.php"); exit;
}
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];
        header("Location: dashboard.php"); exit;
    }
    $error = "Invalid email or password.";
}
$page_title = "Login";
include "partials/header.php";
?>
<div class="row justify-content-center">
<div class="col-md-5">
<div class="card shadow-sm p-4 mt-5">
    <div class="text-center mb-4">
        <div class="display-5">📚</div>
        <h2>Smart Library</h2>
        <p class="text-muted">Sign in to continue</p>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label class="form-label">Email</label>
        <input class="form-control mb-3" type="email" name="email" required>
        <label class="form-label">Password</label>
        <input class="form-control mb-3" type="password" name="password" required>
        <button class="btn btn-primary w-100">Login</button>
    </form>
    <div class="alert alert-info mt-3 mb-0 small">
        Demo Admin: <b>admin@library.com</b> / <b>admin123</b>
    </div>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>
