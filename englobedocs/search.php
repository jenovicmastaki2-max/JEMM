<?php
session_start();
require_once __DIR__ . '/config/database.php';
$q = trim($_GET['q'] ?? '');
$limit = 12;
header('Content-Type: application/json');
if($q === ''){ echo json_encode([]); exit; }
$term = "%" . str_replace('%','\\%',$q) . "%";
$stmt = $pdo->prepare('SELECT id, title, author, cover_image, type FROM documents WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC LIMIT ?');
$stmt->execute([$term, $term, $limit]);
$rows = $stmt->fetchAll();
// Build simplified response
$out = array_map(function($r){
  return ['id'=> (int)$r['id'], 'title'=>$r['title'], 'author'=>$r['author'], 'cover'=> $r['cover_image'], 'type'=>$r['type']];
}, $rows);
echo json_encode($out);
exit;
