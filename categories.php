<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'] ?: null;
    $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)")->execute([$name,$desc]);
    header('Location: categories.php'); 
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    header('Location: categories.php'); 
    exit;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Categories</title>
  <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
<div class="card">
  <h2>Categories</h2>
  <form method="post" style="margin-bottom:12px">
    <input type="text" name="name" placeholder="Category name" required>
    <input type="text" name="description" placeholder="Description">
    <button class="btn" name="create" type="submit">Add</button>
  </form>

  <ul>
    <?php foreach($cats as $c): ?>
      <li>
        <?=htmlspecialchars($c['name'])?>
        <?php if ($c['description']): ?>
          – <?=htmlspecialchars($c['description'])?>
        <?php endif; ?>
        - <a class="btn" style="background:#dc2626" href="?delete=<?=$c['id']?>" onclick="return confirm('Delete this category?')">Delete</a>
      </li>
    <?php endforeach;?>
  </ul>
</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
</body>
</html>
