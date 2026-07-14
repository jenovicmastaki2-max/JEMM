<?php
session_start();
require_once __DIR__ . '/config/database.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /englobedocs/index.php'); exit; }
if(!isset($_SESSION['user'])) { header('Location: /englobedocs/login.php'); exit; }
$doc_id = intval($_POST['document_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
if(!$doc_id || $content === ''){ header('Location: /englobedocs/document.php?id=' . $doc_id); exit; }
$uid = $_SESSION['user']['id'];
$ins = $pdo->prepare('INSERT INTO comments (user_id, document_id, content, created_at) VALUES (?, ?, ?, NOW())');
$ins->execute([$uid, $doc_id, $content]);
header('Location: /englobedocs/document.php?id=' . $doc_id);
exit;
