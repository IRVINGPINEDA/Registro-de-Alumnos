<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Db;
use App\Http\Response;
use App\Support\Flash;
use App\Support\View;

final class HomeController
{
    /**
     * @param array<string,string> $_params
     */
    public function index(array $_params): Response
    {
        $pdo = Db::pdo();

        $groups = $pdo->query(
            'SELECT g.id, g.code, g.carrera_name, g.turno_name, g.grado, g.group_number
             FROM groups g
             JOIN careers c ON c.code = g.carrera_code AND c.is_active = 1
             ORDER BY g.code ASC'
        )->fetchAll();

        $careers = $pdo->query('SELECT id, code, name, is_active FROM careers ORDER BY name ASC')->fetchAll();

        $students = $pdo->query(
            'SELECT s.id, s.nombre, s.apellido_paterno, s.apellido_materno, s.is_active, g.code AS group_code
             FROM students s
             JOIN groups g ON g.id = s.group_id
             ORDER BY s.is_active DESC, s.id DESC'
        )->fetchAll();

        $flash = [
            'success' => Flash::get('success'),
            'error' => Flash::get('error'),
            'errors' => Flash::get('errors', []),
            'old' => Flash::get('old', []),
        ];

        $body = View::render('home', [
            'groups' => $groups,
            'careers' => $careers,
            'students' => $students,
            'flash' => $flash,
        ]);

        return Response::html($body);
    }
}
