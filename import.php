<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $f = $_FILES['csv']['tmp_name'];
    if (($handle = fopen($f, "r")) !== FALSE) {
        $row = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row==0) { $row++; continue; } 
            [$name,$tag,$serial,$catName,$location,$pdate,$cost,$life,$status] = array_map('trim',$data + array_fill(0,9,null));
            $catId = null;
            if ($catName) {
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                $stmt->execute([$catName]);
                $c = $stmt->fetch();
                if ($c) $catId = $c['id'];
                else {
                    $insert = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $insert->execute([$catName]);
                    $catId = $pdo->lastInsertId();
                }
            }
            $ins = $pdo->prepare("INSERT INTO assets (name, asset_tag, serial_number, category_id, location, purchase_date, purchase_cost, useful_life_years, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$name,$tag,$serial,$catId,$location,$pdate,$cost,$life,$status ?: 'available']);
        }
        fclose($handle);
        echo "<div class='card'>Import complete. <a href='index.php'>Back</a></div>";
    }
}
?>
<div class="card">
  <h2>Import CSV</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="csv" accept=".csv" required>
    <button class="btn" type="submit">Upload</button>
  </form>
  <p>CSV header: name,asset_tag,serial_number,category,location,purchase_date,purchase_cost,useful_life_years,status</p>
</div>
<?php require_once __DIR__.'/inc/footer.php'; ?>
