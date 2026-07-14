<?php
session_start();
require_once __DIR__ . '/config/database.php';

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
  if(empty($password)) $errors[] = 'Mot de passe requis.';

  if(empty($errors)){
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if($user && password_verify($password, $user['password'])){
      unset($user['password']);
      $_SESSION['user'] = $user;
      header('Location: /englobedocs/dashboard.php');
      exit;
    } else {
      $errors[] = 'Email ou mot de passe invalide.';
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — EnglobeDocs</title>
  <link rel="stylesheet" href="/englobedocs/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/header.php'; ?>
<main class="container">
  <h2>Se connecter</h2>
  <?php if($errors): ?><div class="errors"><?php foreach($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?></div><?php endif; ?>
  <form method="post" class="form">
    <label>Email<input type="email" name="email" required></label>
    <label>Mot de passe<input type="password" name="password" required></label>
    <button class="btn">Se connecter</button>
  </form>
</main>
<?php include __DIR__ . '/views/footer.php'; ?>
</body>
</html>
