<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Db;
use App\Http\Response;
use App\Support\Careers;
use App\Support\Flash;
use App\Support\View;

final class StudentsController
{
    /**
     * @param array<string,string> $_params
     */
    public function store(array $_params): Response
    {
        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'apellido_paterno' => trim((string)($_POST['apellido_paterno'] ?? '')),
            'apellido_materno' => trim((string)($_POST['apellido_materno'] ?? '')),
            'group_id' => (int)($_POST['group_id'] ?? 0),
        ];

        $errors = $this->validate($data);
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $_POST);
            Flash::set('error', 'Revisa el formulario del alumno.');
            return Response::redirect('/');
        }

        $pdo = Db::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO students (nombre, apellido_paterno, apellido_materno, group_id)
             VALUES (:nombre, :ap, :am, :group_id)'
        );
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':ap' => $data['apellido_paterno'],
            ':am' => $data['apellido_materno'],
            ':group_id' => $data['group_id'],
        ]);

        Flash::set('success', 'Alumno registrado.');
        return Response::redirect('/');
    }

    /**
     * @param array<string,string> $params
     */
    public function show(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();
        $stmt = $pdo->prepare(
            'SELECT s.*, g.code AS group_code, g.carrera_name, g.turno_name, g.grado
             FROM students s
             JOIN groups g ON g.id = s.group_id
             WHERE s.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();
        if (!$student) {
            return Response::html('<h1>404</h1><p>Alumno no encontrado.</p>', 404);
        }

        $flash = [
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
        ];

        return Response::html(View::render('student_view', [
            'student' => $student,
            'flash' => $flash,
        ]));
    }

    /**
     * @param array<string,string> $params
     */
    public function edit(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();

        $studentStmt = $pdo->prepare('SELECT * FROM students WHERE id = :id');
        $studentStmt->execute([':id' => $id]);
        $student = $studentStmt->fetch();
        if (!$student) {
            return Response::html('<h1>404</h1><p>Alumno no encontrado.</p>', 404);
        }

        $groups = $pdo->query(
            'SELECT g.id, g.code, COALESCE(c.is_active, 0) AS career_active
             FROM groups g
             LEFT JOIN careers c ON c.code = g.carrera_code
             ORDER BY g.code ASC'
        )->fetchAll();

        $flash = [
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
            'errors' => Flash::get('errors', []),
            'old' => Flash::get('old', []),
        ];

        return Response::html(View::render('student_edit', [
            'student' => $student,
            'groups' => $groups,
            'flash' => $flash,
        ]));
    }

    /**
     * @param array<string,string> $params
     */
    public function update(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();

        $existsStmt = $pdo->prepare('SELECT id, group_id FROM students WHERE id = :id');
        $existsStmt->execute([':id' => $id]);
        $existing = $existsStmt->fetch();
        if (!$existing) {
            return Response::html('<h1>404</h1><p>Alumno no encontrado.</p>', 404);
        }
        $currentGroupId = (int)($existing['group_id'] ?? 0);

        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'apellido_paterno' => trim((string)($_POST['apellido_paterno'] ?? '')),
            'apellido_materno' => trim((string)($_POST['apellido_materno'] ?? '')),
            'group_id' => (int)($_POST['group_id'] ?? 0),
        ];

        $errors = $this->validate($data, $currentGroupId);
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $_POST);
            Flash::set('error', 'Revisa el formulario del alumno.');
            return Response::redirect('/students/' . $id . '/edit');
        }

        $stmt = $pdo->prepare(
            'UPDATE students
             SET nombre = :nombre, apellido_paterno = :ap, apellido_materno = :am, group_id = :group_id
             WHERE id = :id'
        );
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':ap' => $data['apellido_paterno'],
            ':am' => $data['apellido_materno'],
            ':group_id' => $data['group_id'],
            ':id' => $id,
        ]);

        Flash::set('success', 'Alumno actualizado.');
        return Response::redirect('/students/' . $id);
    }

    /**
     * @param array<string,string> $params
     */
    public function destroy(array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Db::pdo();

        try {
            $stmt = $pdo->prepare(
                "UPDATE students
                 SET is_active = 0, disabled_at = datetime('now')
                 WHERE id = :id AND is_active = 1"
            );
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                $existsStmt = $pdo->prepare('SELECT id FROM students WHERE id = :id');
                $existsStmt->execute([':id' => $id]);
                if (!$existsStmt->fetch()) {
                    return Response::html('<h1>404</h1><p>Alumno no encontrado.</p>', 404);
                }
            }
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo inhabilitar el alumno.');
            return Response::redirect('/');
        }

        Flash::set('success', 'Alumno inhabilitado.');
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
                'UPDATE students
                 SET is_active = 1, disabled_at = NULL
                 WHERE id = :id AND is_active = 0'
            );
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                $existsStmt = $pdo->prepare('SELECT id FROM students WHERE id = :id');
                $existsStmt->execute([':id' => $id]);
                if (!$existsStmt->fetch()) {
                    return Response::html('<h1>404</h1><p>Alumno no encontrado.</p>', 404);
                }
            }
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo habilitar el alumno.');
            return Response::redirect('/');
        }

        Flash::set('success', 'Alumno habilitado.');
        return Response::redirect('/');
    }

    /**
     * @param array{nombre:string,apellido_paterno:string,apellido_materno:string,group_id:int} $data
     * @return array<string,string>
     */
    private function validate(array $data, ?int $currentGroupId = null): array
    {
        $errors = [];
        if ($data['nombre'] === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if ($data['apellido_paterno'] === '') {
            $errors['apellido_paterno'] = 'El apellido paterno es obligatorio.';
        }
        if ($data['apellido_materno'] === '') {
            $errors['apellido_materno'] = 'El apellido materno es obligatorio.';
        }
        if ($data['group_id'] <= 0) {
            $errors['group_id'] = 'Selecciona un grupo.';
        } else {
            $pdo = Db::pdo();
            $stmt = $pdo->prepare('SELECT id, carrera_code FROM groups WHERE id = :id');
            $stmt->execute([':id' => $data['group_id']]);
            $group = $stmt->fetch();
            if (!$group) {
                $errors['group_id'] = 'El grupo seleccionado no existe.';
            } else {
                $selectedId = (int)($group['id'] ?? 0);
                if ($currentGroupId !== null && $selectedId === $currentGroupId) {
                    return $errors;
                }

                $carreraCode = (string)($group['carrera_code'] ?? '');
                if (Careers::name($carreraCode, true) === null) {
                    $errors['group_id'] = 'El grupo pertenece a una carrera inhabilitada.';
                }
            }
        }
        return $errors;
    }
}
