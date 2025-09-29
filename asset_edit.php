<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

$asset = $pdo->prepare("SELECT * FROM assets WHERE id = ?");
$asset->execute([$id]);
$a = $asset->fetch();
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
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE assets SET name=?, asset_tag=?, serial_number=?, category_id=?, location=?, purchase_date=?, purchase_cost=?, useful_life_years=?, status=? WHERE id=?");
    $stmt->execute([$name,$tag,$serial,$category_id,$location,$purchase_date,$cost,$life,$status,$id]);

    $user_id = $_SESSION['user']['id'] ?? null;
    $aud = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, data) VALUES (?, 'update', 'assets', ?, ?)");
    $aud->execute([$user_id, $id, json_encode($_POST)]);

    header('Location: index.php');
    exit;
}
?>
<div class="card">
  <h2>Edit Asset</h2>
  <form method="post">
    <label>Name <input type="text" name="name" value="<?=htmlspecialchars($a['name'])?>" required></label>
    <label>Asset Tag <input type="text" name="asset_tag" value="<?=htmlspecialchars($a['asset_tag'])?>" required></label>
    <label>Serial Number <input type="text" name="serial_number" value="<?=htmlspecialchars($a['serial_number'])?>"></label>
    <label>Category
      <select name="category_id">
        <option value="">-- none --</option>
        <?php foreach($cats as $c): ?>
          <option value="<?=$c['id']?>" <?=($a['category_id']==$c['id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach;?>
      </select>
    </label>
    <label>Location <input type="text" name="location" value="<?=htmlspecialchars($a['location'])?>"></label>
    <label>Purchase Date <input type="date" name="purchase_date" value="<?=htmlspecialchars($a['purchase_date'])?>"></label>
    <label>Purchase Cost <input type="number" step="0.01" name="purchase_cost" value="<?=htmlspecialchars($a['purchase_cost'])?>"></label>
    <label>Useful Life (years) <input type="number" name="useful_life_years" value="<?=htmlspecialchars($a['useful_life_years'])?>"></label>
    <label>Status
      <select name="status">
        <?php foreach(['available','assigned','maintenance','retired'] as $s): ?>
          <option value="<?=$s?>" <?=($a['status']==$s)?'selected':''?>><?=ucfirst($s)?></option>
        <?php endforeach;?>
      </select>
    </label>
    <div style="margin-top:8px">
      <button class="btn" type="submit">Update</button>
      <a class="btn" style="background:#6b7280" href="index.php">Cancel</a>
    </div>
  </form>
</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
