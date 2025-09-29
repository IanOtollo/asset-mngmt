<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';
?>

<link rel="stylesheet" href="assets/css/style.css">

<?php
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $tag = $_POST['asset_tag'];
    $serial = $_POST['serial_number'] ?: null;
    $category_id = $_POST['category_id'] ?: null;
    $location = $_POST['location'] ?: null;
    $purchase_date = $_POST['purchase_date'] ?: null;
    $cost = $_POST['purchase_cost'] !== '' ? $_POST['purchase_cost'] : null;
    $life = $_POST['useful_life_years'] !== '' ? intval($_POST['useful_life_years']) : null;
    $stmt = $pdo->prepare("INSERT INTO assets (name, asset_tag, serial_number, category_id, location, purchase_date, purchase_cost, useful_life_years, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $user_id = $_SESSION['user']['id'] ?? null;
    $stmt->execute([$name, $tag, $serial, $category_id, $location, $purchase_date, $cost, $life, $user_id]);

    $last = $pdo->lastInsertId();
    $aud = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, data) VALUES (?, 'create', 'assets', ?, ?)");
    $aud->execute([$user_id, $last, json_encode(['name'=>$name,'tag'=>$tag])]);

    header('Location: index.php');
    exit;
}
?>
<div class="card">
  <h2>Add Asset</h2>
  <form method="post">
    <label>Name <input type="text" name="name" required></label>
    <label>Asset Tag <input type="text" name="asset_tag" required></label>
    <label>Serial Number <input type="text" name="serial_number"></label>
    <label>Category
      <select name="category_id">
        <option value="">-- none --</option>
        <?php foreach($cats as $c): ?>
          <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach;?>
      </select>
    </label>
    <label>Location <input type="text" name="location"></label>
    <label>Purchase Date <input type="date" name="purchase_date"></label>
    <label>Purchase Cost <input type="number" step="0.01" name="purchase_cost"></label>
    <label>Useful Life (years) <input type="number" name="useful_life_years"></label>
    <div style="margin-top:8px">
      <button class="btn" type="submit">Save</button>
      <a class="btn" style="background:#6b7280" href="index.php">Cancel</a>
    </div>
  </form>
</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
