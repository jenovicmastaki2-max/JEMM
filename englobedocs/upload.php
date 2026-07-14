<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/csrf.php';
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin'){
  header('Location: /englobedocs/login.php');
  exit;
}
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(!verify_csrf_token($_POST['csrf_token'] ?? '')){
    $errors[] = 'Requête invalide (CSRF).';
  } else {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $type = ($_POST['type'] ?? 'free') === 'premium' ? 'premium' : 'free';

    if(!$title) $errors[] = 'Le titre est requis.';
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

    if(empty($errors)){
      $uploads_dir = __DIR__ . '/uploads';
      if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);
      $pdf_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_',basename($pdf['name']));
      if(!move_uploaded_file($pdf['tmp_name'], $uploads_dir . '/' . $pdf_name)){
        $errors[] = 'Impossible d\'enregistrer le PDF.';
      }
      // Generate thumbnail using Imagick if available
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
        } catch(Exception $e){ /* ignore thumbnail */ }
      } else {
        // GD fallback: simple placeholder or try to use Ghostscript (not implemented)
      }

      if(empty($errors)){
        $stmt = $pdo->prepare('INSERT INTO documents (title, description, category_id, author, file_path, cover_image, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $category_id ?: null, $author, $pdf_name, $cover_name, $type]);
        header('Location: /englobedocs/admin.php');
        exit;
      }
    }
  }
}
$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ajouter un document — EnglobeDocs (Async)</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="wrap">
  <h2>Ajouter un document (asynchrone)</h2>
  <?php if($errors): ?><div class="errors"><?php foreach($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?></div><?php endif; ?>
  <form id="upload-form" method="post" enctype="multipart/form-data" class="form">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
    <label>Titre<input type="text" name="title" id="title" required></label>
    <label>Description<textarea name="description" id="description"></textarea></label>
    <label>Auteur<input type="text" name="author" id="author"></label>
    <label>Catégorie<select name="category_id" id="category_id"><option value="">-- Aucune --</option><?php foreach($cats as $c) echo '<option value="'.intval($c['id']).'">'.htmlspecialchars($c['name']).'</option>'; ?></select></label>
    <label>Type<select name="type" id="type"><option value="free">Gratuit</option><option value="premium">Premium</option></select></label>
    <label>PDF<input type="file" name="pdf" id="pdf" accept="application/pdf" required></label>
    <label>Image de couverture (optionnelle)<input type="file" name="cover" id="cover" accept="image/*"></label>
    <button class="btn" type="button" id="start-upload">Uploader</button>
  </form>

  <div id="upload-status" style="margin-top:1rem;display:none">
    <div style="background:rgba(255,255,255,0.04);padding:.5rem;border-radius:6px">
      <div>Progress: <span id="progress-percent">0%</span></div>
      <div style="background:#081226;border-radius:6px;height:12px;margin-top:.5rem"><div id="progress-bar" style="height:12px;width:0;background:linear-gradient(90deg,#6c63ff,#00c6ff);border-radius:6px"></div></div>
      <div id="upload-logs" style="margin-top:.5rem;font-size:.9rem;opacity:.9"></div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
<script src="/englobedocs/assets/js/main.js"></script>
</body>
</html>
