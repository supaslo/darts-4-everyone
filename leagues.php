<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

$pdo = get_db();
$leagues = $pdo->query('SELECT id, name FROM leagues WHERE is_active = 1 ORDER BY name')->fetchAll();
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
      <section class="section-header">
        <p class="eyebrow">Sign-ups</p>
        <h1>Sign up for upcoming leagues</h1>

        <?php if ($success): ?>
          <p class="alert alert-success">Thanks! Your team has been signed up successfully.</p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="signup.php" method="post" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <label for="league_id">League</label>
          <select id="league_id" name="league_id" required>
            <option value="">-- Select a league --</option>
            <?php foreach ($leagues as $league): ?>
                <option value="<?= (int) $league['id'] ?>" <?= (isset($old['league_id']) && (int) $old['league_id'] === (int) $league['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($league['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
          </select>
        
          <label for="team_name">Team Name</label>
          <input type="text" id="team_name" name="team_name" maxlength="100" required
               value="<?= htmlspecialchars($old['team_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <label for="captain_name">Captain's Name</label>
          <input type="text" id="captain_name" name="captain_name" maxlength="100" required
               value="<?= htmlspecialchars($old['captain_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <label for="captain_phone">Captain's Phone Number</label>
          <input type="tel" id="captain_phone" name="captain_phone" maxlength="20" required
               value="<?= htmlspecialchars($old['captain_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <label for="captain_email">Captain's Email Address</label>
          <input type="email" id="captain_email" name="captain_email" maxlength="150" required
               value="<?= htmlspecialchars($old['captain_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <fieldset>
            <legend>Partners (optional, up to 3)</legend>
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <label for="partner<?= $i ?>_name">Partner <?= $i ?> Name</label>
                <input type="text" id="partner<?= $i ?>_name" name="partner<?= $i ?>_name" maxlength="100"
                       value="<?= htmlspecialchars($old["partner{$i}_name"] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <?php endfor; ?>
          </fieldset>

          <button type="submit">Sign Up</button>
        </form>
      </section>
    </main>

    <footer class="site-footer">
      <p>&copy; 2026 Darts 4 Everyone</p>
    </footer>
  </div>
</body>
</html>
