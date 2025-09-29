<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/header.php';

$q = $_GET['q'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

$params = [];
$where = [];
if ($q !== '') {
    $where[] = "(a.name LIKE ? OR a.asset_tag LIKE ? OR a.serial_number LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($category_filter !== '') {
    $where[] = "c.id = ?";
    $params[] = $category_filter;
}
if ($status_filter !== '') {
    $where[] = "a.status = ?";
    $params[] = $status_filter;
}

$sql = "SELECT a.*, c.name as category_name 
        FROM assets a 
        LEFT JOIN categories c ON a.category_id = c.id";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY a.created_at DESC LIMIT 500";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<div class="card">
  <h2>Assets</h2>
  <form method="get" class="form-row" style="align-items:center">
    <input type="text" name="q" placeholder="search name, tag, serial" value="<?=htmlspecialchars($q)?>">
    <select name="category">
      <option value="">All categories</option>
      <?php foreach($cats as $cat): ?>
        <option value="<?=$cat['id']?>" <?=($category_filter==$cat['id'])?'selected':''?>><?=htmlspecialchars($cat['name'])?></option>
      <?php endforeach; ?>
    </select>
    <select name="status">
      <option value="">All status</option>
      <?php foreach(['available','assigned','maintenance','retired'] as $s): ?>
        <option value="<?=$s?>" <?=($status_filter==$s)?'selected':''?>><?=ucfirst($s)?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Search</button>

    <a class="btn" style="background:#10b981" href="asset_add.php">+ Add Asset</a>
    <a class="btn" style="background:#374151" href="export.php">Export CSV</a>
  </form>
  <table class="table" style="margin-top:12px">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Tag</th><th>Serial</th>
        <th>Category</th><th>Purchase Date</th>
        <th>Cost</th><th>Dep/yr</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($assets as $a): 
        $dep = ($a['purchase_cost'] && $a['useful_life_years']) 
          ? number_format($a['purchase_cost'] / $a['useful_life_years'], 2) 
          : 'N/A';
      ?>
      <tr>
        <td><?=$a['id']?></td>
        <td><?=htmlspecialchars($a['name'])?></td>
        <td><?=htmlspecialchars($a['asset_tag'])?></td>
        <td><?=htmlspecialchars($a['serial_number'])?></td>
        <td><?=htmlspecialchars($a['category_name'])?></td>
        <td><?=htmlspecialchars($a['purchase_date'])?></td>
        <td><?=$a['purchase_cost'] !== null ? number_format($a['purchase_cost'],2) : ''?></td>
        <td><?=$dep?></td>
        <td><?=htmlspecialchars($a['status'])?></td>
        <td>

          <a href="asset_view.php?id=<?=$a['id']?>">View</a> |
          <a href="asset_edit.php?id=<?=$a['id']?>">Edit</a> |
          <a href="asset_delete.php?id=<?=$a['id']?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
