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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dart League Sign Up</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Dart League Sign Up</h1>

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
</div>
</body>
</html>
