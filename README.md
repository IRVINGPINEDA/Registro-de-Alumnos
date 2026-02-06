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
**AARON SALVADOR ROJAS MONROY**
- https://github.com/IRVINGPINEDA
- https://github.com/AARONROJAS

**JOSHUA MEDINA**
- https://github.com/goku58432
