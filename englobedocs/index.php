<?php
session_start();
require_once __DIR__ . '/config/database.php';
function isLoggedIn() {
    return isset($_SESSION['user']);
}
function isAdmin() {
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>EnglobeDocs — Home</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="container">
  <section class="hero">
    <h1>Bienvenue sur EnglobeDocs</h1>
    <p>Plateforme locale XAMPP de gestion de documents PDF — demo.</p>
    <?php if(!isLoggedIn()): ?>
      <p><a class="btn" href="/englobedocs/register.php">S'inscrire</a> <a class="btn outline" href="/englobedocs/login.php">Se connecter</a></p>
    <?php else: ?>
      <p><a class="btn" href="/englobedocs/dashboard.php">Accéder au dashboard</a></p>
    <?php endif; ?>
  </section>

  <section class="documents">
    <h2>Documents récents</h2>
    <div class="cards">
    <?php
      $stmt = $pdo->query("SELECT id, title, cover_image, type FROM documents ORDER BY created_at DESC LIMIT 6");
      $docs = $stmt->fetchAll();
      if($docs){
        foreach($docs as $d){
          echo '<article class="card">';
          echo '<img src="/englobedocs/uploads/' . htmlspecialchars($d['cover_image'] ?? 'placeholder.png') . '" alt="cover">';
          echo '<div class="card-body"><h3>' . htmlspecialchars($d['title']) . '</h3>';
          if($d['type']==='premium') echo '<span class="badge premium">Premium</span>'; else echo '<span class="badge pdf">PDF</span>';
          echo '<p><a class="btn small" href="#">Voir</a></p></div></article>';
        }
      } else {
        echo '<p>Aucun document pour le moment.</p>';
      }
    ?>
    </div>
  </section>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
<script src="/englobedocs/assets/js/main.js"></script>
</body>
</html>
