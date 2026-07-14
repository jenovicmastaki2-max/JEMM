<?php
session_start();
require_once __DIR__ . '/config/database.php';
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin'){
  header('Location: /englobedocs/login.php'); exit;
}
$id = intval($_GET['id'] ?? 0);
if(!$id) { header('Location: /englobedocs/admin.php'); exit; }
// fetch
$stmt = $pdo->prepare('SELECT * FROM documents WHERE id = ?'); $stmt->execute([$id]); $doc = $stmt->fetch();
if(!$doc) { header('Location: /englobedocs/admin.php'); exit; }
$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $author = trim($_POST['author'] ?? '');
  $type = ($_POST['type'] ?? 'free') === 'premium' ? 'premium' : 'free';
  $category_id = intval($_POST['category_id'] ?? 0) ?: null;
  if(!$title) $errors[] = 'Titre requis.';
  if(empty($errors)){
    $stmt = $pdo->prepare('UPDATE documents SET title=?, description=?, category_id=?, author=?, type=? WHERE id=?');
    $stmt->execute([$title,$description,$category_id,$author,$type,$id]);
    header('Location: /englobedocs/admin.php'); exit;
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editer</title><link rel="stylesheet" href="/englobedocs/assets/css/style.css"></head><body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="wrap">
  <h2>Éditer le document</h2>
  <?php if($errors) echo '<div class="errors">'.implode('',array_map('htmlspecialchars',$errors)).'</div>'; ?>
  <form method="post" class="form">
    <label>Titre<input type="text" name="title" value="<?php echo htmlspecialchars($doc['title']); ?>"></label>
    <label>Description<textarea name="description"><?php echo htmlspecialchars($doc['description']); ?></textarea></label>
    <label>Auteur<input type="text" name="author" value="<?php echo htmlspecialchars($doc['author']); ?>"></label>
    <label>Type<select name="type"><option value="free" <?php if($doc['type']=='free') echo 'selected'; ?>>Gratuit</option><option value="premium" <?php if($doc['type']=='premium') echo 'selected'; ?>>Premium</option></select></label>
    <label>Catégorie<select name="category_id"><option value="">-- Aucune --</option><?php foreach($cats as $c) echo '<option value="'.intval($c['id']).'"'.($doc['category_id']==$c['id']?' selected':'').'>'.htmlspecialchars($c['name']).'</option>'; ?></select></label>
    <button class="btn">Enregistrer</button>
  </form>
  <form method="post" action="/englobedocs/delete_document.php" style="margin-top:1rem">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <button class="btn outline" onclick="return confirm('Supprimer ce document ?')">Supprimer</button>
  </form>
</main>
<?php include __DIR__ . '/views/footer.php'; ?></body></html>
