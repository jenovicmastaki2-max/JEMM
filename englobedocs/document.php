<?php
session_start();
require_once __DIR__ . '/config/database.php';
$id = intval($_GET['id'] ?? 0);
if(!$id){ header('Location: /englobedocs/index.php'); exit; }
$stmt = $pdo->prepare('SELECT d.*, c.name AS category_name FROM documents d LEFT JOIN categories c ON d.category_id = c.id WHERE d.id = ?');
$stmt->execute([$id]);
doc = $stmt->fetch();
if(!$doc){ header('Location: /englobedocs/index.php'); exit; }
// Comments
$comments = $pdo->prepare('SELECT cm.*, u.name FROM comments cm JOIN users u ON cm.user_id = u.id WHERE cm.document_id = ? ORDER BY cm.created_at DESC');
$comments->execute([$id]);
$comments = $comments->fetchAll();
// Is favorite
$is_fav = false;
if(isset($_SESSION['user'])){
  $f = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND document_id = ?');
  $f->execute([$_SESSION['user']['id'], $id]);
  $is_fav = (bool)$f->fetchColumn();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars($doc['title']); ?> — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="wrap">
  <div class="doc-detail">
    <h1><?php echo htmlspecialchars($doc['title']); ?></h1>
    <p class="meta">Auteur: <?php echo htmlspecialchars($doc['author']); ?> • Catégorie: <?php echo htmlspecialchars($doc['category_name'] ?? '—'); ?> • <?php echo htmlspecialchars($doc['type']); ?></p>
    <?php if($doc['cover_image']): ?>
      <img src="/englobedocs/uploads/<?php echo htmlspecialchars($doc['cover_image']); ?>" alt="cover" style="max-width:240px;border-radius:8px;">
    <?php endif; ?>
    <p><?php echo nl2br(htmlspecialchars($doc['description'])); ?></p>

    <div style="margin-top:1rem">
      <?php if(isset($_SESSION['user'])): ?>
        <form id="fav-form" action="/englobedocs/favorite.php" method="post" style="display:inline">
          <input type="hidden" name="document_id" value="<?php echo $id; ?>">
          <button class="btn" type="submit"><?php echo $is_fav ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?></button>
        </form>
      <?php endif; ?>
      <a class="btn outline" href="/englobedocs/download.php?id=<?php echo $id; ?>">Télécharger</a>
    </div>

    <section style="margin-top:2rem">
      <h3>Commentaires</h3>
      <?php if(isset($_SESSION['user'])): ?>
        <form method="post" action="/englobedocs/comment.php" class="form" style="max-width:600px">
          <input type="hidden" name="document_id" value="<?php echo $id; ?>">
          <label>Message<textarea name="content" required></textarea></label>
          <button class="btn">Envoyer</button>
        </form>
      <?php else: ?>
        <p><a href="/englobedocs/login.php">Connectez-vous</a> pour commenter.</p>
      <?php endif; ?>

      <div class="comments">
        <?php foreach($comments as $c): ?>
          <div class="card" style="padding:.75rem;margin-bottom:.5rem">
            <strong><?php echo htmlspecialchars($c['name']); ?></strong>
            <small style="opacity:.7"> — <?php echo htmlspecialchars($c['created_at']); ?></small>
            <p><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>