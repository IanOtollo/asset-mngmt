<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Asset Management</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="topbar">
  <div class="container">
    <a href="/asset-mngmt/index.php" class="brand">
  <img src="/asset-mngmt/images/busia-logo.png" alt="Busia County Logo" class="logo">
  AssetMgmt
</a>
    <div class="navlinks">
      <a href="index.php">Assets</a>
      <a href="asset_add.php">Add</a>
      <a href="categories.php">Categories</a>
      <a href="assign_asset.php">Assign</a>
      <a href="import.php">Import</a>
      <a href="export.php">Export</a>
      <a href="audit_log.php">Audit</a>
      <?php if(current_user()): ?>
        <span class="muted">
          Hello, <?=htmlspecialchars(current_user()['full_name'] ?? current_user()['username'])?>
        </span>
        <a href="users.php?action=logout">Logout</a>
      <?php else: ?>
        <a href="users.php">Users</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container main">
