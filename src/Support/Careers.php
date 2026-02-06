<?php
declare(strict_types=1);

namespace App\Support;

use App\Db;
use PDO;

final class Careers
{
    /**
     * @return array<string,string>
     */
    public static function defaults(): array
    {
        return [
            'ADM' => 'Administración de Empresas',
            'AET' => 'Administración de Empresas Turísticas',
            'RIN' => 'Relaciones Internacionales',
            'CPF' => 'Contaduría Pública y Finanzas',
            'DER' => 'Derecho',
            'MEP' => 'Mercadotecnia y Publicidad',
            'GAS' => 'Gastronomía',
            'PCC' => 'Periodismo y Ciencias de la Comunicación',
            'DMO' => 'Diseño de Modas',
            'PED' => 'Pedagogía',
            'CFD' => 'Cultura Física y Educación del Deporte',
            'IDI' => 'Idiomas (Inglés y Francés)',
            'PSI' => 'Psicología',
            'DIN' => 'Diseño de Interiores',
            'DGR' => 'Diseño Gráfico',
            'ILT' => 'Ingeniería en Logística y Transporte',
            'IAR' => 'Ingeniero Arquitecto',
            'IAF' => 'Informática Administrativa y Fiscal',
            'ISC' => 'Ingeniería en Sistemas Computacionales',
            'IMA' => 'Ingeniería Mecánica Automotriz',
        ];
    }

    public static function seedDefaults(PDO $pdo): void
    {
        $stmt = $pdo->prepare('INSERT OR IGNORE INTO careers (code, name, is_active) VALUES (:code, :name, 1)');
        foreach (self::defaults() as $code => $name) {
            $stmt->execute([
                ':code' => $code,
                ':name' => $name,
            ]);
        }
    }

    /**
     * @return array<int,array{id:int,code:string,name:string,is_active:int,disabled_at:?string}>
     */
    public static function all(): array
    {
        $pdo = Db::pdo();
        return $pdo->query('SELECT id, code, name, is_active, disabled_at FROM careers ORDER BY name ASC')->fetchAll();
    }

    /**
     * @return array<string,string>
     */
    public static function activeMap(): array
    {
        $pdo = Db::pdo();
        $rows = $pdo->query('SELECT code, name FROM careers WHERE is_active = 1 ORDER BY name ASC')->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[(string)$r['code']] = (string)$r['name'];
        }
        return $map;
    }

    public static function name(string $code, bool $onlyActive = true): ?string
    {
        $pdo = Db::pdo();
        $sql = 'SELECT name FROM careers WHERE code = :code';
        if ($onlyActive) {
            $sql .= ' AND is_active = 1';
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        return $row ? (string)$row['name'] : null;
    }

    public static function generateUniqueCode(PDO $pdo, string $name): string
    {
        $base = self::generateBaseCode($name);
        $code = $base;
        $n = 2;
        while (self::codeExists($pdo, $code)) {
            $code = $base . (string)$n;
            $n++;
        }
        return $code;
    }

    private static function codeExists(PDO $pdo, string $code): bool
    {
        $stmt = $pdo->prepare('SELECT 1 FROM careers WHERE code = :code');
        $stmt->execute([':code' => $code]);
        return (bool)$stmt->fetchColumn();
    }

    private static function generateBaseCode(string $name): string
    {
        $name = trim($name);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $s = strtoupper($ascii !== false ? $ascii : $name);

        $parts = preg_split('/[^A-Z0-9]+/', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stop = ['DE', 'DEL', 'LA', 'EL', 'LOS', 'LAS', 'Y', 'EN'];
        $parts = array_values(array_filter($parts, static fn (string $p): bool => !in_array($p, $stop, true)));
        if ($parts === []) {
            $parts = preg_split('/[^A-Z0-9]+/', strtoupper($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $code = '';
        foreach ($parts as $p) {
            $code .= $p[0] ?? '';
            if (strlen($code) >= 3) {
                break;
            }
        }

        if (strlen($code) < 3) {
            $letters = preg_replace('/[^A-Z0-9]/', '', $s) ?: '';
            $code = substr($letters, 0, 3);
        }

        $code = strtoupper($code);
        if ($code === '') {
            $code = 'CAR';
        }
        return $code;
    }
}
