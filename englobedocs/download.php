<?php
session_start();
require_once __DIR__ . '/config/database.php';
$id = intval($_GET['id'] ?? 0);
if(!$id) { header('Location: /englobedocs/index.php'); exit; }
$stmt = $pdo->prepare('SELECT file_path, title FROM documents WHERE id = ?');
$stmt->execute([$id]);
$doc = $stmt->fetch();
if(!$doc){ header('Location: /englobedocs/index.php'); exit; }
$file = __DIR__ . '/uploads/' . $doc['file_path'];
if(!file_exists($file)) { header('Location: /englobedocs/index.php'); exit; }
// Record download
$uid = $_SESSION['user']['id'] ?? null;
$ins = $pdo->prepare('INSERT INTO downloads (user_id, document_id, created_at) VALUES (?, ?, NOW())');
$ins->execute([$uid, $id]);
// Serve file
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.basename($doc['title']).'.pdf"');
header('Content-Length: '.filesize($file));
readfile($file);
exit;
