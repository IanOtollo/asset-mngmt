<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$logs = $pdo->query("
    SELECT al.*, u.username 
    FROM audit_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 500
")->fetchAll();
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="card">
  <h2>Audit Log</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Time</th>
        <th>User</th>
        <th>Action</th>
        <th>Target</th>
        <th>Target ID</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($logs as $l): ?>
        <tr>
          <td><?=htmlspecialchars($l['created_at'])?></td>
          <td><?=htmlspecialchars($l['username'])?></td>
          <td><?=htmlspecialchars($l['action'])?></td>
          <td><?=htmlspecialchars($l['target_table'])?></td>
          <td><?=htmlspecialchars($l['target_id'])?></td>
          <td><pre style="white-space:pre-wrap"><?=htmlspecialchars($l['data'])?></pre></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
