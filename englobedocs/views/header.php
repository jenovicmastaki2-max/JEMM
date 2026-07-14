<header class="site-header">
  <div class="wrap">
    <h1 class="logo"><a href="/englobedocs/index.php">EnglobeDocs</a></h1>
    <nav>
      <a href="/englobedocs/index.php">Home</a>
      <?php if(isset($_SESSION['user'])): ?>
        <a href="/englobedocs/dashboard.php">Dashboard</a>
        <?php if(($_SESSION['user']['role'] ?? '') === 'admin'): ?>
          <a href="/englobedocs/admin.php">Admin</a>
        <?php endif; ?>
        <a href="/englobedocs/logout.php">Logout</a>
      <?php else: ?>
        <a href="/englobedocs/login.php">Login</a>
        <a href="/englobedocs/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
