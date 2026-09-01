<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signupform.php');
    exit;
}

function back_with_errors(array $errors, array $old): void
{
    $_SESSION['form_errors'] = $errors;
    unset($old['csrf_token']);
    $_SESSION['form_old'] = $old;
    header('Location: signupform.php');
    exit;
}

// CSRF check.
$token = $_POST['csrf_token'] ?? '';
if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    back_with_errors(['Your session expired. Please try again.'], $_POST);
}

$errors = [];

$leagueId = filter_input(INPUT_POST, 'league_id', FILTER_VALIDATE_INT);
$teamName = trim((string) ($_POST['team_name'] ?? ''));
$captainName = trim((string) ($_POST['captain_name'] ?? ''));
$captainPhone = trim((string) ($_POST['captain_phone'] ?? ''));
$captainEmail = trim((string) ($_POST['captain_email'] ?? ''));
$partners = [];
for ($i = 1; $i <= 3; $i++) {
    $partners[$i] = trim((string) ($_POST["partner{$i}_name"] ?? ''));
}

if (!$leagueId) {
    $errors[] = 'Please select a league.';
}
if ($teamName === '') {
    $errors[] = 'Team name is required.';
}
if ($captainName === '') {
    $errors[] = "Captain's name is required.";
}
if ($captainPhone === '' || !preg_match('/^[0-9()+\-.\s]{7,20}$/', $captainPhone)) {
    $errors[] = 'Please enter a valid phone number.';
}
if ($captainEmail === '' || !filter_var($captainEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

$pdo = get_db();

if ($leagueId) {
    $stmt = $pdo->prepare('SELECT id FROM leagues WHERE id = :id AND is_active = 1');
    $stmt->execute(['id' => $leagueId]);
    if (!$stmt->fetch()) {
        $errors[] = 'The selected league is not valid.';
    }
}

if (!empty($errors)) {
    back_with_errors($errors, $_POST);
}

$stmt = $pdo->prepare('
    INSERT INTO signups (league_id, team_name, captain_name, captain_phone, captain_email, partner1_name, partner2_name, partner3_name)
    VALUES (:league_id, :team_name, :captain_name, :captain_phone, :captain_email, :partner1, :partner2, :partner3)
');
$stmt->execute([
    'league_id' => $leagueId,
    'team_name' => $teamName,
    'captain_name' => $captainName,
    'captain_phone' => $captainPhone,
    'captain_email' => $captainEmail,
    'partner1' => $partners[1] !== '' ? $partners[1] : null,
    'partner2' => $partners[2] !== '' ? $partners[2] : null,
    'partner3' => $partners[3] !== '' ? $partners[3] : null,
]);

// Rotate the CSRF token after successful use.
unset($_SESSION['csrf_token']);
header('Location: leagues.php?success=1');
exit;
