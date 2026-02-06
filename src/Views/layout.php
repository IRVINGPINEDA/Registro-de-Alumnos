<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars((string)($title ?? 'PRACTICA')) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f7f7fb; }
    .card { border: 0; box-shadow: 0 6px 20px rgba(0,0,0,.06); }
    .code-input { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    .sidebar { width: 280px; }
    @media (min-width: 992px) {
      .sidebar { height: calc(100vh - 56px); position: sticky; top: 56px; }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container-fluid px-4">
    <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
      Menú
    </button>
    <a class="navbar-brand fw-semibold" href="/">Registro</a>
    <span class="navbar-text text-muted">Alumnos y grupos</span>
  </div>
</nav>

<div class="d-flex">
  <div class="offcanvas-lg offcanvas-start bg-white border-end sidebar" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="sidebarLabel">Acciones</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0">
      <div class="p-3">
        <div class="list-group list-group-flush">
          <a class="list-group-item list-group-item-action" href="/#students" data-panel="students">Inicio</a>
          <a class="list-group-item list-group-item-action" href="/#register-student" data-panel="register-student">Registrar alumno</a>
          <a class="list-group-item list-group-item-action" href="/#register-group" data-panel="register-group">Registrar grupo</a>
          <a class="list-group-item list-group-item-action" href="/#catalogs" data-panel="catalogs">Conf. catálogos</a>
        </div>
        <hr class="my-3">
        <div class="text-muted small">
          Tip: en móvil, abre el menú para navegar.
        </div>
      </div>
    </div>
  </div>

  <main class="container-fluid py-4 px-4 flex-grow-1">
    <?= $content ?? '' ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $scripts ?? '' ?>
</body>
</html>
