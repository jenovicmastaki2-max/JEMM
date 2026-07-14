<?php
session_start();
require_once __DIR__ . '/config/database.php';
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin'){
  header('Location: /englobedocs/login.php');
  exit;
}
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Validate inputs
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $author = trim($_POST['author'] ?? '');
  $category_id = intval($_POST['category_id'] ?? 0);
  $type = ($_POST['type'] ?? 'free') === 'premium' ? 'premium' : 'free';

  if(!$title) $errors[] = 'Le titre est requis.';

  // File uploads
  if(!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK){
    $errors[] = 'Le fichier PDF est requis.';
  } else {
    $pdf = $_FILES['pdf'];
    $allowed = ['application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $pdf['tmp_name']);
    finfo_close($finfo);
    if(!in_array($mime, $allowed)) $errors[] = 'Seuls les PDF sont autorisés.';
    if($pdf['size'] > 50 * 1024 * 1024) $errors[] = 'Fichier trop volumineux (max 50MB).';
  }

  // Cover image optional
  $cover_name = null;
  if(isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK){
    $cover = $_FILES['cover'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $cover['tmp_name']);
    finfo_close($finfo);
    $img_allowed = ['image/jpeg','image/png','image/webp'];
    if(!in_array($mime, $img_allowed)) $errors[] = 'Image de couverture invalide (jpg/png/webp).';
  }

  if(empty($errors)){
    // Move files
    $uploads_dir = __DIR__ . '/uploads';
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);
    $pdf_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_',basename($pdf['name']));
    if(!move_uploaded_file($pdf['tmp_name'], $uploads_dir . '/' . $pdf_name)){
      $errors[] = 'Impossible d\'enregistrer le PDF.';
    }
    if(isset($cover) && $cover['error'] === UPLOAD_ERR_OK){
      $cover_name = time() . '_cover_' . preg_replace('/[^a-zA-Z0-9._-]/','_',basename($cover['name']));
      move_uploaded_file($cover['tmp_name'], $uploads_dir . '/' . $cover_name);
    }

    if(empty($errors)){
      $stmt = $pdo->prepare('INSERT INTO documents (title, description, category_id, author, file_path, cover_image, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
      $stmt->execute([$title, $description, $category_id ?: null, $author, $pdf_name, $cover_name, $type]);
      header('Location: /englobedocs/admin.php');
      exit;
    }
  }
}
// Fetch categories
$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ajouter un document — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="wrap">
  <h2>Ajouter un document</h2>
  <?php if($errors): ?><div class="errors"><?php foreach($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="form">
    <label>Titre<input type="text" name="title" required></label>
    <label>Description<textarea name="description"></textarea></label>
    <label>Auteur<input type="text" name="author"></label>
    <label>Catégorie<select name="category_id"><option value="">-- Aucune --</option><?php foreach($cats as $c) echo '<option value="'.intval($c['id']).'">'.htmlspecialchars($c['name']).'</option>'; ?></select></label>
    <label>Type<select name="type"><option value="free">Gratuit</option><option value="premium">Premium</option></select></label>
    <label>PDF<input type="file" name="pdf" accept="application/pdf" required></label>
    <label>Image de couverture<input type="file" name="cover" accept="image/*"></label>
    <button class="btn">Uploader</button>
  </form>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>