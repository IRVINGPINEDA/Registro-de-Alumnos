<?php
declare(strict_types=1);

namespace App;

use PDO;

final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            throw new \RuntimeException('DB not initialized. Call Db::init() first.');
        }
        return self::$pdo;
    }

    public static function init(): void
    {
        if (self::$pdo !== null) {
            return;
        }

        $storageDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }

        $dbPath = $storageDir . DIRECTORY_SEPARATOR . 'app.sqlite';
        self::$pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$pdo->exec('PRAGMA foreign_keys = ON;');

        self::migrate();
    }

    private static function migrate(): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS careers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  code TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL UNIQUE,
  is_active INTEGER NOT NULL DEFAULT 1,
  disabled_at TEXT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  carrera_code TEXT NOT NULL,
  carrera_name TEXT NOT NULL,
  turno_code TEXT NOT NULL,
  turno_name TEXT NOT NULL,
  grado INTEGER NOT NULL,
  group_number INTEGER NOT NULL,
  code TEXT NOT NULL UNIQUE,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE UNIQUE INDEX IF NOT EXISTS groups_unique_combo
  ON groups (carrera_code, turno_code, grado, group_number);

CREATE TABLE IF NOT EXISTS students (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  apellido_paterno TEXT NOT NULL,
  apellido_materno TEXT NOT NULL,
  group_id INTEGER NOT NULL,
  is_active INTEGER NOT NULL DEFAULT 1,
  disabled_at TEXT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE RESTRICT
);
SQL;
        self::$pdo->exec($sql);

        $careersCols = self::$pdo->query("PRAGMA table_info('careers')")->fetchAll();
        if ($careersCols === []) {
            throw new \RuntimeException('No se pudo crear la tabla careers.');
        }

        \App\Support\Careers::seedDefaults(self::$pdo);

        $cols = self::$pdo->query("PRAGMA table_info('students')")->fetchAll();
        $names = array_map(static fn (array $c): string => (string)($c['name'] ?? ''), $cols);

        if (!in_array('is_active', $names, true)) {
            self::$pdo->exec("ALTER TABLE students ADD COLUMN is_active INTEGER NOT NULL DEFAULT 1;");
        }
        if (!in_array('disabled_at', $names, true)) {
            self::$pdo->exec("ALTER TABLE students ADD COLUMN disabled_at TEXT NULL;");
        }
    }
}
