<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$id = $_GET['id'] ?? null;
if (!$id) { 
    header('Location: index.php'); 
    exit; 
}

$stmt = $pdo->prepare("SELECT a.*, c.name as category_name 
                       FROM assets a 
                       LEFT JOIN categories c ON a.category_id=c.id 
                       WHERE a.id=?");
$stmt->execute([$id]);
$a = $stmt->fetch();

if (!$a) {
    echo "<div class='card'><p>Asset not found.</p>
          <a class='btn' href='index.php'>Back</a></div>";
    require_once __DIR__.'/inc/footer.php';
    exit;
}

$dep_per_year = ($a['purchase_cost'] && $a['useful_life_years']) 
    ? number_format($a['purchase_cost'] / $a['useful_life_years'], 2) 
    : 'N/A';
?>

<div class="card">
  <h2>Asset: <?=htmlspecialchars($a['name'])?></h2>
  <p><strong>Tag:</strong> <?=htmlspecialchars($a['asset_tag'])?></p>
  <p><strong>Serial:</strong> <?=htmlspecialchars($a['serial_number'])?></p>
  <p><strong>Category:</strong> <?=htmlspecialchars($a['category_name'])?></p>
  <p><strong>Location:</strong> <?=htmlspecialchars($a['location'])?></p>
  <p><strong>Purchase Date:</strong> <?=htmlspecialchars($a['purchase_date'])?></p>
  <p><strong>Cost:</strong> <?= $a['purchase_cost'] !== null ? number_format($a['purchase_cost'],2) : '' ?></p>
  <p><strong>Useful life (years):</strong> <?=htmlspecialchars($a['useful_life_years'])?></p>
  <p><strong>Depreciation / year:</strong> <?=$dep_per_year?></p>
  <p><strong>Status:</strong> <?=htmlspecialchars($a['status'])?></p>
  
  <a class="btn" href="asset_edit.php?id=<?=$a['id']?>">Edit</a>
  <a class="btn" style="background:#6b7280" href="index.php">Back</a>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
