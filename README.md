<<<<<<< HEAD
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
=======
# Registro de Alumnos

Proyecto hecho en PHP y MySQL para la gestion de alumnos de una universidad

---

## Tecnologias utilizadas

- MYSQL
- PHP
- SQL Lite
- **Git / GitHub**

---

## Arquitectura del proyecto

Aplicacion web con:

- **Frontend estatico** (HTML/CSS/JS) servido como archivos estaticos.
- **Backend** (Node.js + Express) con endpoints REST bajo `/api/*`.
- **Base de datos** SQLite (generada localmente).

---

## Estructura del proyecto (actual)
>>>>>>> c33326f53782c03ff2848542668f08f636221fd5

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

<<<<<<< HEAD
> Nota: `storage/` se genera en runtime (SQLite + archivos temporales).
=======
---

## Trabajo en equipo y ramas

Ramas:

- `main` -> version estable
- `irving/*` -> dev
- `aaron/*` -> dev

---

## Requisitos
- PHP 8.1+ (recomendado 8.2+)

## Ejecutar
Desde la raíz del proyecto:

powershell
php -S localhost:8000 -t public


Luego abre http://localhost:8000.

## Base de datos
Usa SQLite en storage/app.sqlite. Se crea automáticamente al primer inicio.

---

## Funcionalidades

- Registro de Alumnos
- Registro de Grupo
- Registro de Turno
- Configuracion de Catalogos
- Registro de Carreras
- Agregar Nombre
- Agregar Apellido Paterno
- Agregar Apellido Materno

---

## Autores

**IRVING ISAY PINEDA PINEDA**
- https://github.com/IRVINGPINEDA

**AARON ROJAS**
- https://github.com/aaronrojas
>>>>>>> c33326f53782c03ff2848542668f08f636221fd5
