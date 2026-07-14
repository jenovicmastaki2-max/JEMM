<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/csrf.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /englobedocs/index.php'); exit; }
if(!isset($_SESSION['user'])) { header('Location: /englobedocs/login.php'); exit; }
if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ header('Location: /englobedocs/index.php'); exit; }
$doc_id = intval($_POST['document_id'] ?? 0);
$uid = $_SESSION['user']['id'];
// Toggle favorite
$exists = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND document_id = ?');
$exists->execute([$uid, $doc_id]);
if($exists->fetch()){
  $del = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND document_id = ?');
  $del->execute([$uid, $doc_id]);
} else {
  $ins = $pdo->prepare('INSERT IGNORE INTO favorites (user_id, document_id, created_at) VALUES (?, ?, NOW())');
  $ins->execute([$uid, $doc_id]);
}
header('Location: /englobedocs/document.php?id=' . $doc_id);
exit;
