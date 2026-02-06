<?php
declare(strict_types=1);

use App\Support\View;

$title = 'Editar alumno';
$errors = $flash['errors'] ?? [];
$old = $flash['old'] ?? [];

$value = static function (string $key) use ($old, $student): string {
    if (array_key_exists($key, $old)) {
        return (string)$old[$key];
    }
    return (string)($student[$key] ?? '');
};

ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 mb-0">Editar alumno</h1>
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
    <form method="post" action="/students/<?= (int)$student['id'] ?>/update" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Nombre</label>
        <input name="nombre" class="form-control" value="<?= htmlspecialchars($value('nombre')) ?>">
        <?php if (isset($errors['nombre'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['nombre']) ?></div><?php endif; ?>
      </div>
      <div class="col-md-4">
        <label class="form-label">Apellido paterno</label>
        <input name="apellido_paterno" class="form-control" value="<?= htmlspecialchars($value('apellido_paterno')) ?>">
        <?php if (isset($errors['apellido_paterno'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['apellido_paterno']) ?></div><?php endif; ?>
      </div>
      <div class="col-md-4">
        <label class="form-label">Apellido materno</label>
        <input name="apellido_materno" class="form-control" value="<?= htmlspecialchars($value('apellido_materno')) ?>">
        <?php if (isset($errors['apellido_materno'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['apellido_materno']) ?></div><?php endif; ?>
      </div>
      <div class="col-md-4">
        <label class="form-label">Grupo</label>
        <select name="group_id" class="form-select">
          <option value="">Selecciona...</option>
          <?php foreach ($groups as $g): ?>
            <?php
              $gid = (int)($g['id'] ?? 0);
              $isCurrent = ((string)$value('group_id') === (string)$gid);
              $careerActive = (int)($g['career_active'] ?? 0);
              $disabled = ($careerActive !== 1) && !$isCurrent;
              $label = (string)($g['code'] ?? '');
              if ($careerActive !== 1) {
                  $label .= ' (Carrera inhabilitada)';
              }
            ?>
            <option value="<?= $gid ?>" <?= $isCurrent ? 'selected' : '' ?> <?= $disabled ? 'disabled' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['group_id'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['group_id']) ?></div><?php endif; ?>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary">Guardar cambios</button>
        <a class="btn btn-outline-secondary" href="/students/<?= (int)$student['id'] ?>">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
$scripts = '';

echo View::render('layout', compact('title', 'content', 'scripts'));
