<?php
session_start();
require_once __DIR__ . '/config/database.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /englobedocs/admin.php'); exit; }
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { header('Location: /englobedocs/login.php'); exit; }
$id = intval($_POST['id'] ?? 0);
if(!$id){ header('Location: /englobedocs/admin.php'); exit; }
// delete file paths
$stmt = $pdo->prepare('SELECT file_path, cover_image FROM documents WHERE id = ?'); $stmt->execute([$id]); $d = $stmt->fetch();
if($d){
  if(!empty($d['file_path'])) @unlink(__DIR__ . '/uploads/' . $d['file_path']);
  if(!empty($d['cover_image'])) @unlink(__DIR__ . '/uploads/' . $d['cover_image']);
}
$del = $pdo->prepare('DELETE FROM documents WHERE id = ?'); $del->execute([$id]);
header('Location: /englobedocs/admin.php'); exit;
