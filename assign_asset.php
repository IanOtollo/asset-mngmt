<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$assets = $pdo->query("SELECT id, name FROM assets WHERE status='available' ORDER BY name")->fetchAll();
$users = $pdo->query("SELECT id, full_name, username FROM users ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_id   = $_POST['asset_id'] ?? null;
    $user_id    = $_POST['user_id'] ?: null;
    $location   = trim($_POST['location'] ?? '') ?: null;
    $notes      = trim($_POST['notes'] ?? '') ?: null;
    $assigned_by = $_SESSION['user']['id'] ?? null;

    if ($asset_id) {
        $stmt = $pdo->prepare("INSERT INTO assignments (asset_id,user_id,location,assigned_by,notes) VALUES (?,?,?,?,?)");
        $stmt->execute([$asset_id, $user_id, $location, $assigned_by, $notes]);

        $pdo->prepare("UPDATE assets SET status='assigned' WHERE id=?")->execute([$asset_id]);

        $aud = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, data) VALUES (?, 'assign', 'assignments', ?, ?)");
        $aud->execute([$assigned_by, $pdo->lastInsertId(), json_encode(['asset'=>$asset_id,'user'=>$user_id])]);
    }

    header('Location: index.php');
    exit;
}
?>

<div class="card">
  <h2>Assign Asset</h2>

  <?php if (!$assets): ?>
    <p>No available assets to assign.</p>
    <a class="btn" href="index.php">Back</a>
  <?php else: ?>
    <form method="post">
      <label>Asset
        <select name="asset_id" required>
          <option value="">-- select asset --</option>
          <?php foreach($assets as $as): ?>
            <option value="<?=$as['id']?>"><?=htmlspecialchars($as['name'])?></option>
          <?php endforeach;?>
        </select>
      </label>

      <label>Assign to user
        <select name="user_id">
          <option value="">-- assign to location only --</option>
          <?php foreach($users as $u): ?>
            <option value="<?=$u['id']?>"><?=htmlspecialchars($u['full_name'] ?: $u['username'])?></option>
          <?php endforeach;?>
        </select>
      </label>

      <label>Location <input type="text" name="location"></label>
      <label>Notes <textarea name="notes"></textarea></label>

      <div style="margin-top:8px">
        <button class="btn" type="submit">Assign</button>
        <a class="btn" style="background:#6b7280" href="index.php">Cancel</a>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require_once __DIR__.'/inc/footer.php'; ?>
