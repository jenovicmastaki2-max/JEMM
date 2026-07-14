<?php
// Enhance header with search form
?>
<header class="site-header">
  <div class="wrap">
    <h1 class="logo"><a href="/englobedocs/index.php">EnglobeDocs</a></h1>
    <nav>
      <a href="/englobedocs/index.php">Home</a>
      <?php if(isset($_SESSION['user'])): ?>
        <a href="/englobedocs/dashboard.php">Dashboard</a>
        <?php if(($_SESSION['user']['role'] ?? '') === 'admin'): ?>
          <a href="/englobedocs/admin.php">Admin</a>
          <a href="/englobedocs/upload.php">Ajouter</a>
        <?php endif; ?>
        <a href="/englobedocs/logout.php">Logout</a>
      <?php else: ?>
        <a href="/englobedocs/login.php">Login</a>
        <a href="/englobedocs/register.php">Register</a>
      <?php endif; ?>
    </nav>
    <form id="search-form" action="/englobedocs/search.php" method="get" style="margin-left:1rem">
      <input id="search-input" name="q" placeholder="Rechercher des documents..." style="padding:.4rem;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:var(--text);">
    </form>
  </div>
  <div id="search-results" style="position:relative;max-width:1100px;margin:0 auto"></div>
</header>
