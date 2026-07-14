<?php
session_start();
require_once __DIR__ . '/config/database.php';
if(!isset($_SESSION['user'])){
  header('Location: /englobedocs/login.php');
  exit;
}
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="container">
  <h2>Bonjour, <?php echo htmlspecialchars($user['name']); ?></h2>
  <section class="dashboard-cards">
    <div class="stat">Utilisateurs: <?php echo $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); ?></div>
    <div class="stat">PDF: <?php echo $pdo->query('SELECT COUNT(*) FROM documents')->fetchColumn(); ?></div>
    <div class="stat">Téléchargements: <?php echo $pdo->query('SELECT COUNT(*) FROM downloads')->fetchColumn(); ?></div>
  </section>

  <section>
    <h3>Documents</h3>
    <div class="cards">
      <?php
        $stmt = $pdo->query("SELECT id, title, cover_image, type FROM documents ORDER BY created_at DESC LIMIT 12");
        $docs = $stmt->fetchAll();
        foreach($docs as $d){
          echo '<article class="card">';
          echo '<img src="/englobedocs/uploads/' . htmlspecialchars($d['cover_image'] ?? 'placeholder.png') . '" alt="cover">';
          echo '<div class="card-body"><h3>' . htmlspecialchars($d['title']) . '</h3>';
          echo '<p><a class="btn small" href="#"> Télécharger </a></p></div></article>';
        }
      ?>
    </div>
  </section>

</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>
