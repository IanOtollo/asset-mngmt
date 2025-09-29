<?php
require_once __DIR__.'/inc/db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=assets_export_'.date('Ymd_His').'.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','name','asset_tag','serial_number','category','location','purchase_date','purchase_cost','useful_life_years','status']);
$stmt = $pdo->query("SELECT a.*, c.name as category_name FROM assets a LEFT JOIN categories c ON a.category_id=c.id ORDER BY a.id DESC");
while ($r = $stmt->fetch()) {
    fputcsv($out, [$r['id'],$r['name'],$r['asset_tag'],$r['serial_number'],$r['category_name'],$r['location'],$r['purchase_date'],$r['purchase_cost'],$r['useful_life_years'],$r['status']]);
}
fclose($out);
exit;
