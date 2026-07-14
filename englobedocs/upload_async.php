<?php
// Asynchronous upload endpoint returns JSON
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/csrf.php';
header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['error'=>'Invalid method']); exit; }
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { echo json_encode(['error'=>'Unauthorized']); exit; }
if(!verify_csrf_token($_POST['csrf_token'] ?? '')){ echo json_encode(['error'=>'CSRF']); exit; }
if(!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK){ echo json_encode(['error'=>'No pdf']); exit; }
$pdf = $_FILES['pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $pdf['tmp_name']);
finfo_close($finfo);
if($mime !== 'application/pdf'){ echo json_encode(['error'=>'Not pdf']); exit; }
$uploads_dir = __DIR__ . '/uploads'; if(!is_dir($uploads_dir)) mkdir($uploads_dir,0755,true);
$pdf_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_',basename($pdf['name']));
if(!move_uploaded_file($pdf['tmp_name'], $uploads_dir . '/' . $pdf_name)){
  echo json_encode(['error'=>'Move failed']); exit;
}
// Attempt thumbnail generation
$cover_name = null;
if(class_exists('Imagick')){
  try{
    $im = new Imagick();
    $im->setResolution(150,150);
    $im->readImage($uploads_dir . '/' . $pdf_name . '[0]');
    $im->setImageFormat('jpeg');
    $im->thumbnailImage(300, 0);
    $cover_name = time() . '_thumb_' . preg_replace('/[^a-zA-Z0-9._-]/','_',pathinfo($pdf_name, PATHINFO_FILENAME)) . '.jpg';
    $im->writeImage($uploads_dir . '/' . $cover_name);
    $im->clear(); $im->destroy();
  } catch(Exception $e){ }
}
// Insert DB record
$title = substr(trim($_POST['title'] ?? pathinfo($pdf['name'], PATHINFO_FILENAME)),0,255);
$description = trim($_POST['description'] ?? '');
$author = trim($_POST['author'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0) ?: null;
$type = ($_POST['type'] ?? 'free') === 'premium' ? 'premium' : 'free';
$stmt = $pdo->prepare('INSERT INTO documents (title, description, category_id, author, file_path, cover_image, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
$stmt->execute([$title, $description, $category_id, $author, $pdf_name, $cover_name, $type]);
$docId = $pdo->lastInsertId();
echo json_encode(['ok'=>true,'docId'=> (int)$docId, 'cover'=>$cover_name, 'file'=>$pdf_name]);
exit;
