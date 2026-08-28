<?php
declare(strict_types=1);

$db = require '/home/u941905604/domains/darts4everyone.com/db_config.php';

function get_db(): PDO
{
    static $pdo = null;
    global $db;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $db['host'],
        $db['port'] ?? 3306,
        $db['database']
    );

    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS leagues (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL UNIQUE,
            is_active TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS signups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            league_id INT NOT NULL,
            team_name VARCHAR(100) NOT NULL,
            captain_name VARCHAR(100) NOT NULL,
            captain_phone VARCHAR(20) NOT NULL,
            captain_email VARCHAR(150) NOT NULL,
            partner1_name VARCHAR(100) DEFAULT NULL,
            partner2_name VARCHAR(100) DEFAULT NULL,
            partner3_name VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (league_id) REFERENCES leagues(id)
        ) ENGINE=InnoDB
    ');

    // Seed a few sample leagues if the table is empty.
    $count = (int) $pdo->query('SELECT COUNT(*) FROM leagues')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO leagues (name) VALUES (:name)');
        foreach ([
            'Monday Night Doubles',
            'Tuesday Cricket League',
            'Wednesday 501 League',
            'Thursday Mixed Doubles',
        ] as $name) {
            $stmt->execute(['name' => $name]);
        }
    }

    return $pdo;
}
