<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/auth.php';
require_once __DIR__.'/inc/header.php';

if (isset($_GET['action']) && $_GET['action']=='logout') {
    logout_user();
    header('Location: users.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (login_user($pdo, $username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials";
    }
}

if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $full = $_POST['full_name'];
    $role = $_POST['role'];
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username,$hash,$full,$role]);
    header('Location: users.php'); exit;
}

$users = $pdo->query("SELECT id, username, full_name, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<div class="card">
  <h2>Login</h2>
  <?php if (!$error): ?>
  <?php else: ?>
    <div style="color:#b91c1c"><?=$error?></div>
  <?php endif;?>
  <?php if(!isset($_SESSION['user'])): ?>
    <form method="post">
      <input type="text" name="username" placeholder="username" required>
      <input type="password" name="password" placeholder="password" required>
      <button class="btn" name="login" type="submit">Login</button>
    </form>
  <?php else: ?>
    <h3>Users</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Username</th><th>Full name</th><th>Role</th></tr></thead>
      <tbody>
        <?php foreach($users as $u): ?>
          <tr><td><?=$u['id']?></td><td><?=htmlspecialchars($u['username'])?></td><td><?=htmlspecialchars($u['full_name'])?></td><td><?=$u['role']?></td></tr>
        <?php endforeach;?>
      </tbody>
    </table>

    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
      <h3>Create user</h3>
      <form method="post">
        <input type="text" name="username" placeholder="username" required>
        <input type="password" name="password" placeholder="password" required>
        <input type="text" name="full_name" placeholder="Full name">
        <select name="role"><option value="viewer">viewer</option><option value="manager">manager</option><option value="admin">admin</option></select>
        <button class="btn" name="create_user" type="submit">Create</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
