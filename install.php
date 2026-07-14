<?php
// Simple install helper (runs SQL migrations and ensures uploads dir exists)
// Usage: php install.php
require_once __DIR__ . '/englobedocs/config/database.php';
$sqlFiles = [__DIR__ . '/database.sql', __DIR__ . '/migrations/20260714_add_purchases.sql'];
foreach($sqlFiles as $f){
  if(!file_exists($f)) continue;
  echo "Importing $f ...\n";
  $sql = file_get_contents($f);
  try{
    $pdo->exec($sql);
  } catch (PDOException $e){
    echo "Error importing $f: " . $e->getMessage() . "\n";
  }
}
// Create uploads
$u = __DIR__ . '/englobedocs/uploads';
if(!is_dir($u)) mkdir($u,0755,true);
echo "Uploads folder ready: $u\n";
echo "Done.\n";
