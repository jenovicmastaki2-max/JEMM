<?php
session_start();
require_once __DIR__ . '/config/database.php';
// Simulated purchase endpoint (stub). In production, integrate Stripe / PayPal SDK.
if($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /englobedocs/index.php'); exit; }
if(!isset($_SESSION['user'])){ header('Location: /englobedocs/login.php'); exit; }
$doc_id = intval($_POST['document_id'] ?? 0);
// In a real integration, redirect to provider and handle webhook
// For demo: insert a purchase record and allow download
$stmt = $pdo->prepare('SELECT id, title, type, price_cents FROM documents WHERE id = ?'); $stmt->execute([$doc_id]); $doc = $stmt->fetch();
if(!$doc){ header('Location: /englobedocs/index.php'); exit; }
$price = intval($doc['price_cents'] ?? 0);
$ins = $pdo->prepare('INSERT INTO purchases (user_id, document_id, amount_cents, provider, created_at) VALUES (?, ?, ?, ?, NOW())');
$ins->execute([$_SESSION['user']['id'], $doc_id, $price, 'stub']);
header('Location: /englobedocs/document.php?id=' . $doc_id);
exit;
