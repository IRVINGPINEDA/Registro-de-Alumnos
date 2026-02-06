<?php
declare(strict_types=1);

use App\Support\View;

$title = 'Detalle del alumno';

ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 mb-0">Detalle del alumno</h1>
    <div class="text-muted">ID: <?= (int)$student['id'] ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="/">Volver</a>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars((string)$flash['success']) ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars((string)$flash['error']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Nombre</dt>
      <dd class="col-sm-9"><?= htmlspecialchars(trim($student['nombre'] . ' ' . $student['apellido_paterno'] . ' ' . $student['apellido_materno'])) ?></dd>
      <dt class="col-sm-3">Grupo</dt>
      <dd class="col-sm-9"><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$student['group_code']) ?></span></dd>
      <dt class="col-sm-3">Carrera</dt>
      <dd class="col-sm-9"><?= htmlspecialchars((string)$student['carrera_name']) ?></dd>
      <dt class="col-sm-3">Turno</dt>
      <dd class="col-sm-9"><?= htmlspecialchars((string)$student['turno_name']) ?></dd>
      <dt class="col-sm-3">Grado</dt>
      <dd class="col-sm-9"><?= (int)$student['grado'] ?></dd>
      <dt class="col-sm-3">Estado</dt>
      <dd class="col-sm-9">
        <?php if ((int)($student['is_active'] ?? 1) === 1): ?>
          <span class="badge text-bg-success">Habilitado</span>
        <?php else: ?>
          <span class="badge text-bg-warning">Inhabilitado</span>
          <?php if (!empty($student['disabled_at'])): ?>
            <span class="text-muted ms-2 small">(<?= htmlspecialchars((string)$student['disabled_at']) ?>)</span>
          <?php endif; ?>
        <?php endif; ?>
      </dd>
      <dt class="col-sm-3">Registrado</dt>
      <dd class="col-sm-9"><?= htmlspecialchars((string)$student['created_at']) ?></dd>
    </dl>

    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-outline-secondary" href="/students/<?= (int)$student['id'] ?>/edit">Editar</a>
      <?php if ((int)($student['is_active'] ?? 1) === 1): ?>
        <form method="post" action="/students/<?= (int)$student['id'] ?>/delete" onsubmit="return confirm('¿Inhabilitar alumno?');">
          <button class="btn btn-outline-danger">Inhabilitar</button>
        </form>
      <?php else: ?>
        <form method="post" action="/students/<?= (int)$student['id'] ?>/enable" onsubmit="return confirm('¿Habilitar alumno?');">
          <button class="btn btn-outline-success">Habilitar</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
$scripts = '';

echo View::render('layout', compact('title', 'content', 'scripts'));
