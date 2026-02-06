<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Db;
use App\Http\Response;
use App\Support\Careers;
use App\Support\Flash;

final class GroupsController
{
    /**
     * @param array<string,string> $_params
     */
    public function preview(array $_params): Response
    {
        $carreraCode = trim((string)($_GET['carrera'] ?? ''));
        $turnoCode = trim((string)($_GET['turno'] ?? ''));
        $grado = (int)($_GET['grado'] ?? 0);

        $preview = $this->computeNextGroup($carreraCode, $turnoCode, $grado);
        if ($preview === null) {
            return Response::json(['ok' => false, 'message' => 'Parámetros inválidos.'], 422);
        }

        return Response::json(['ok' => true] + $preview);
    }

    /**
     * @param array<string,string> $_params
     */
    public function store(array $_params): Response
    {
        $carreraCode = trim((string)($_POST['carrera_code'] ?? ''));
        $carreraName = $this->carreraName($carreraCode);
        $turnoCode = trim((string)($_POST['turno_code'] ?? ''));
        $turnoName = $this->turnoName($turnoCode);
        $grado = (int)($_POST['grado'] ?? 0);

        $errors = [];
        if ($carreraName === null) {
            $errors['carrera_code'] = 'Selecciona una carrera válida.';
        }
        if ($turnoName === null) {
            $errors['turno_code'] = 'Selecciona un turno válido.';
        }
        if ($grado < 1 || $grado > 12) {
            $errors['grado'] = 'El grado debe estar entre 1 y 12.';
        }

        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('old', $_POST);
            Flash::set('error', 'Revisa el formulario de grupo.');
            return Response::redirect('/');
        }

        $next = $this->computeNextGroup($carreraCode, $turnoCode, $grado);
        if ($next === null) {
            Flash::set('error', 'No se pudo generar el código del grupo.');
            return Response::redirect('/');
        }

        $pdo = Db::pdo();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO groups (carrera_code, carrera_name, turno_code, turno_name, grado, group_number, code)
                 VALUES (:carrera_code, :carrera_name, :turno_code, :turno_name, :grado, :group_number, :code)'
            );
            $stmt->execute([
                ':carrera_code' => $carreraCode,
                ':carrera_name' => $carreraName,
                ':turno_code' => $turnoCode,
                ':turno_name' => $turnoName,
                ':grado' => $grado,
                ':group_number' => $next['group_number'],
                ':code' => $next['code'],
            ]);
        } catch (\Throwable $e) {
            Flash::set('error', 'No se pudo registrar el grupo (¿ya existe?).');
            return Response::redirect('/');
        }

        Flash::set('success', 'Grupo registrado: ' . $next['code']);
        return Response::redirect('/');
    }

    private function carreraName(string $code): ?string
    {
        return Careers::name($code);
    }

    private function turnoName(string $code): ?string
    {
        $map = [
            'M' => 'Matutino',
            'V' => 'Vespertino',
        ];
        return $map[$code] ?? null;
    }

    /**
     * @return array{code:string,group_number:int}|null
     */
    private function computeNextGroup(string $carreraCode, string $turnoCode, int $grado): ?array
    {
        if ($this->carreraName($carreraCode) === null) {
            return null;
        }
        if ($this->turnoName($turnoCode) === null) {
            return null;
        }
        if ($grado < 1 || $grado > 12) {
            return null;
        }

        $pdo = Db::pdo();
        $stmt = $pdo->prepare(
            'SELECT MAX(group_number) AS max_num
             FROM groups
             WHERE carrera_code = :carrera AND turno_code = :turno AND grado = :grado'
        );
        $stmt->execute([
            ':carrera' => $carreraCode,
            ':turno' => $turnoCode,
            ':grado' => $grado,
        ]);
        $row = $stmt->fetch();
        $max = (int)($row['max_num'] ?? 0);
        $next = $max + 1;
        if ($next > 99) {
            return null;
        }

        $code = sprintf('%s%d%02d-%s', $carreraCode, $grado, $next, $turnoCode);
        return ['code' => $code, 'group_number' => $next];
    }
}

