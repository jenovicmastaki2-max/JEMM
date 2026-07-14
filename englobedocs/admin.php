<?php
session_start();
require_once __DIR__ . '/config/database.php';
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin'){
  header('Location: /englobedocs/login.php');
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="container">
  <h2>Administration</h2>
  <p>Gérer les documents, utilisateurs et statistiques.</p>
  <section>
    <h3>Documents</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Titre</th><th>Auteur</th><th>Type</th><th>Actions</th></tr></thead>
      <tbody>
      <?php
        $stmt = $pdo->query('SELECT id, title, author, type FROM documents ORDER BY created_at DESC');
        while($row = $stmt->fetch()){
          echo '<tr>';
          echo '<td>'.htmlspecialchars($row['id']).'</td>';
          echo '<td>'.htmlspecialchars($row['title']).'</td>';
          echo '<td>'.htmlspecialchars($row['author']).'</td>';
          echo '<td>'.htmlspecialchars($row['type']).'</td>';
          echo '<td><a class="btn small" href="#">Edit</a> <a class="btn small outline" href="#">Delete</a></td>';
          echo '</tr>';
        }
      ?>
      </tbody>
    </table>
  </section>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>
