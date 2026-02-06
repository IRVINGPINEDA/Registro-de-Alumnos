<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Db;
use App\Http\Response;
use App\Support\Careers;
use App\Support\Flash;

final class CareersController
{
    /**
     * @param array<string,string> $_params
     */
    public function store(array $_params): Response
    {
        $name = trim((string)($_POST['career_name'] ?? ''));
        $errors = [];

        if ($name === '') {
            $errors['career_name'] = 'La carrera es obligatoria.';
        } elseif (mb_strlen($name) > 120) {
            $errors['career_name'] = 'La carrera es demasiado larga.';
        }

        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $_POST);
            Flash::set('error', 'Revisa el formulario de carrera.');
            return Response::redirect('/');
        }

        $pdo = Db::pdo();
        $code = Careers::generateUniqueCode($pdo, $name);

        try {
            $stmt = $pdo->prepare('INSERT INTO careers (code, name, is_active) VALUES (:code, :name, 1)');
            $stmt->execute([
                ':code' => $code,
                ':name' => $name,
            ]);
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo registrar la carrera (¿ya existe?).');
            Flash::set('old', $_POST);
            return Response::redirect('/');
        }

        Flash::set('success', 'Carrera registrada.');
        return Response::redirect('/');
    }

    /**
     * @param array<string,string> $params
     */
    public function disable(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();

        try {
            $stmt = $pdo->prepare(
                "UPDATE careers
                 SET is_active = 0, disabled_at = datetime('now')
                 WHERE id = :id AND is_active = 1"
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                $existsStmt = $pdo->prepare('SELECT id FROM careers WHERE id = :id');
                $existsStmt->execute([':id' => $id]);
                if (!$existsStmt->fetch()) {
                    return Response::html('<h1>404</h1><p>Carrera no encontrada.</p>', 404);
                }
            }
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo inhabilitar la carrera.');
            return Response::redirect('/');
        }

        Flash::set('success', 'Carrera inhabilitada.');
        return Response::redirect('/');
    }

    /**
     * @param array<string,string> $params
     */
    public function enable(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();

        try {
            $stmt = $pdo->prepare(
                'UPDATE careers
                 SET is_active = 1, disabled_at = NULL
                 WHERE id = :id AND is_active = 0'
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                $existsStmt = $pdo->prepare('SELECT id FROM careers WHERE id = :id');
                $existsStmt->execute([':id' => $id]);
                if (!$existsStmt->fetch()) {
                    return Response::html('<h1>404</h1><p>Carrera no encontrada.</p>', 404);
                }
            }
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo habilitar la carrera.');
            return Response::redirect('/');
        }

        Flash::set('success', 'Carrera habilitada.');
        return Response::redirect('/');
    }
}

