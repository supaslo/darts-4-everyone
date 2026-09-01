<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

$pdo = get_db();
$leagues = $pdo->query('SELECT id, name FROM leagues WHERE is_active = 1 ORDER BY name ASC')->fetchAll();
// CSRF token for the form.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);
$success = isset($_GET['success']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Darts 4 Everyone | Leagues</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
  <script src="assets/js/main.js" defer></script>
  <script src="assets/js/leagues.js" defer></script>
</head>
<body>
  <div class="page-shell">
    <header class="site-header" role="banner">
      <div class="brand-block" aria-label="Site branding">
        <img src="assets/img/logo-placeholder.svg" alt="Darts 4 Everyone logo" class="brand-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
        <span class="brand-fallback" aria-hidden="true">D4E</span>
        <div>
          <p class="brand-title">Darts 4 Everyone</p>
          <p class="brand-subtitle">Leagues. Tournaments. Community.</p>
        </div>
      </div>
      <button id="menu-toggle" class="menu-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
      <nav id="site-nav" class="site-nav" aria-label="Primary">
        <a href="index.html" data-page="home">Home</a>
        <a href="leagues.php" data-page="leagues">Leagues</a>
        <a href="signupform.php" data-page="signupform">Sign Up</a>
        <a href="about.html" data-page="about">About</a>
        <a href="contact.html" data-page="contact">Contact Us</a>
      </nav>
    </header>

    <main id="main-content" tabindex="-1">
      <section class="section-header">
        <p class="eyebrow">Stats</p>
        <h1>Schedule and stats in one place</h1>
        <p>Pick a league to view the current match schedule and player/team stats.</p>
        <p><a href="https://www.leagueleader.net/sharedreport.php?operatorid=1872&code=3453fc0d-4183-4aa6-8cb6-e59ed1cc1a96" target="_blank" rel="noopener noreferrer">072026 Schedule</a></p>
        <p><a href="https://www.leagueleader.net/sharedreport.php?operatorid=1872&code=b851d40b-671e-4211-8c09-7440550d1bbf" target="_blank" rel="noopener noreferrer">072026 Stats</a></p>
      </section>
      <?php if ($success): ?>
        <section>
          <p class="alert alert-success">Thanks! Your team has been signed up successfully.</p>
        </section>
      <?php endif; ?>
      <section class="content-grid" aria-label="Highlights">
        <?php if (!empty($leagues)): ?>
            <?php foreach ($leagues as $league): ?>
              <article class="card">
                <?php $teams = $pdo->query('SELECT team_name FROM signups WHERE league_id = ' . (int) $league['id'] . ' ORDER BY created_at DESC')->fetchAll(); ?>
                <h2>
                  <?= htmlspecialchars($league['name'], ENT_QUOTES, 'UTF-8') ?>
                  <?php if (sizeof($teams) < 11): ?>
                    <a href="signupform.php?league=<?= (int) $league['id'] ?>">Sign Up</a>
                  <?php else: ?>
                    <span style="color: red;">FULL</span>
                  <?php endif; ?>
                </h2>
                <?php if (!empty($teams)): ?>
                  <ul>
                    <?php foreach ($teams as $team): ?>
                      <li><?= htmlspecialchars($team['team_name'], ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
                
              </article>
            <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </main>

    <footer class="site-footer">
      <p>&copy; 2026 Darts 4 Everyone</p>
    </footer>
  </div>
</body>
</html>
