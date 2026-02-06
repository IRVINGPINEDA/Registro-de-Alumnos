# PRACTICA - Registro de alumnos y grupos (PHP + SQLite)

Mini-sistema en PHP (sin dependencias) para:
- Registrar grupos (carrera, turno, grado) con código autogenerado.
- Registrar alumnos y asignarlos a un grupo.
- Listar alumnos registrados con acciones: ver, editar y eliminar.

## Requisitos
- PHP 8.1+ (recomendado 8.2+)

## Ejecutar
Desde la raíz del proyecto:

```powershell
php -S localhost:8000 -t public
```

Luego abre `http://localhost:8000`.

## Base de datos
Usa SQLite en `storage/app.sqlite`. Se crea automáticamente al primer inicio.

## Estructura del proyecto

```
.
├── .gitignore
├── README.md
├── public
│   └── index.php
└── src
    ├── bootstrap.php
    ├── Db.php
    ├── Controllers
    │   ├── GroupsController.php
    │   ├── HomeController.php
    │   └── StudentsController.php
    ├── Http
    │   └── Response.php
    ├── Routing
    │   └── Router.php
    ├── Support
    │   ├── Flash.php
    │   └── View.php
    └── Views
        ├── home.php
        ├── layout.php
        ├── student_edit.php
        └── student_view.php
```

> Nota: `storage/` se genera en runtime (SQLite + archivos temporales).
