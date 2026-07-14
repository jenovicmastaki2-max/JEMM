<?php
session_start();
require_once __DIR__ . '/config/database.php';

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';

  if(!$name) $errors[] = 'Le nom est requis.';
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
  if(strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
  if($password !== $password_confirm) $errors[] = 'Les mots de passe ne correspondent pas.';

  if(empty($errors)){
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if($stmt->fetch()){
      $errors[] = 'Un compte existe déjà avec cet email.';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
      $stmt->execute([$name, $email, $hash, 'user']);
      header('Location: /englobedocs/login.php');
      exit;
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="container">
  <h2>Créer un compte</h2>
  <?php if($errors): ?><div class="errors"><?php foreach($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?></div><?php endif; ?>
  <form method="post" class="form">
    <label>Nom<input type="text" name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Mot de passe<input type="password" name="password" required></label>
    <label>Confirmer le mot de passe<input type="password" name="password_confirm" required></label>
    <button class="btn">S'inscrire</button>
  </form>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>
