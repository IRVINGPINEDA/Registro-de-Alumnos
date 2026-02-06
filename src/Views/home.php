<?php
declare(strict_types=1);

use App\Support\View;
use App\Support\Careers;

$title = 'PRACTICA';

/** @var array{success:mixed,error:mixed,errors:array<string,string>,old:array<string,mixed>} $flash */
$errors = $flash['errors'] ?? [];
$old = $flash['old'] ?? [];

ob_start();
?>
<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars((string)$flash['success']) ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars((string)$flash['error']) ?></div>
<?php endif; ?>

<div id="register-student" class="app-panel d-none">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
      <div class="card">
        <div class="card-header bg-white fw-semibold">Registrar alumno</div>
        <div class="card-body">
          <form method="post" action="/students" class="vstack gap-3">
            <div>
              <label class="form-label">Nombre</label>
              <input name="nombre" class="form-control" value="<?= htmlspecialchars((string)($old['nombre'] ?? '')) ?>">
              <?php if (isset($errors['nombre'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['nombre']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Apellido paterno</label>
              <input name="apellido_paterno" class="form-control" value="<?= htmlspecialchars((string)($old['apellido_paterno'] ?? '')) ?>">
              <?php if (isset($errors['apellido_paterno'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['apellido_paterno']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Apellido materno</label>
              <input name="apellido_materno" class="form-control" value="<?= htmlspecialchars((string)($old['apellido_materno'] ?? '')) ?>">
              <?php if (isset($errors['apellido_materno'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['apellido_materno']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Grupo</label>
              <select name="group_id" class="form-select">
                <option value="">Selecciona...</option>
                <?php foreach ($groups as $g): ?>
                  <option value="<?= (int)$g['id'] ?>" <?= ((string)($old['group_id'] ?? '') === (string)$g['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)$g['code']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['group_id'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['group_id']) ?></div><?php endif; ?>
              <?php if (count($groups) === 0): ?><div class="text-muted small mt-1">No hay grupos disponibles. Revisa que exista una carrera y un grupo habilitados.</div><?php endif; ?>
            </div>
            <button class="btn btn-primary w-100" <?= count($groups) === 0 ? 'disabled' : '' ?>>Registrar alumno</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="register-group" class="app-panel d-none">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
      <div class="card">
        <div class="card-header bg-white fw-semibold">Registrar grupo</div>
        <div class="card-body">
          <form method="post" action="/groups" class="vstack gap-3" id="groupForm">
            <div>
              <label class="form-label">Carrera</label>
              <select name="carrera_code" class="form-select" id="carrera">
                <option value="">Selecciona...</option>
                <?php foreach (Careers::activeMap() as $code => $name): ?>
                  <option value="<?= htmlspecialchars((string)$code) ?>" <?= ((string)($old['carrera_code'] ?? '') === (string)$code) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)$name) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['carrera_code'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['carrera_code']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Turno</label>
              <select name="turno_code" class="form-select" id="turno">
                <option value="">Selecciona...</option>
                <option value="M" <?= ((string)($old['turno_code'] ?? '') === 'M') ? 'selected' : '' ?>>Matutino</option>
                <option value="V" <?= ((string)($old['turno_code'] ?? '') === 'V') ? 'selected' : '' ?>>Vespertino</option>
              </select>
              <?php if (isset($errors['turno_code'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['turno_code']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Grado</label>
              <select name="grado" class="form-select" id="grado">
                <option value="">Selecciona...</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="<?= $i ?>" <?= ((string)($old['grado'] ?? '') === (string)$i) ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
              </select>
              <?php if (isset($errors['grado'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['grado']) ?></div><?php endif; ?>
            </div>
            <div>
              <label class="form-label">Grupo (autogenerado)</label>
              <input class="form-control code-input" id="groupCode" value="-" disabled>
              <div class="text-muted small mt-1" id="groupHint">Selecciona carrera, turno y grado.</div>
            </div>
            <button class="btn btn-dark w-100" id="groupSubmit" disabled>Registrar grupo</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="catalogs" class="app-panel d-none">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-white fw-semibold">Conf. Catálogos.</div>
        <div class="card-body">
          <form method="post" action="/careers" class="row g-2 align-items-end">
            <div class="col-12 col-md-7 col-lg-6">
              <label class="form-label">Carrera</label>
              <input name="career_name" class="form-control" placeholder="Ej. Psicología" value="<?= htmlspecialchars((string)($old['career_name'] ?? '')) ?>">
              <?php if (isset($errors['career_name'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['career_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-12 col-md-5 col-lg-3">
              <button class="btn btn-dark w-100">Registrar</button>
            </div>
          </form>

          <div class="table-responsive mt-3">
            <table class="table table-striped table-hover table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Carrera</th>
                  <th class="text-center" style="width:110px">Eliminar</th>
                  <th class="text-center" style="width:110px">Activar</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($careers)): ?>
                  <tr><td colspan="3" class="text-center text-muted py-4">Sin carreras registradas.</td></tr>
                <?php endif; ?>
                <?php foreach (($careers ?? []) as $c): ?>
                  <tr class="<?= ((int)($c['is_active'] ?? 1) === 1) ? '' : 'table-secondary' ?>">
                    <td>
                      <?= htmlspecialchars((string)($c['name'] ?? '')) ?>
                      <span class="text-muted small ms-2">(<?= htmlspecialchars((string)($c['code'] ?? '')) ?>)</span>
                    </td>
                    <td class="text-center">
                      <form method="post" action="/careers/<?= (int)$c['id'] ?>/disable" class="m-0" onsubmit="return confirm('¿Inhabilitar carrera?');">
                        <button class="btn btn-sm btn-outline-danger" <?= ((int)($c['is_active'] ?? 1) === 1) ? '' : 'disabled' ?>>&times;</button>
                      </form>
                    </td>
                    <td class="text-center">
                      <form method="post" action="/careers/<?= (int)$c['id'] ?>/enable" class="m-0" onsubmit="return confirm('¿Habilitar carrera?');">
                        <button class="btn btn-sm btn-outline-success" <?= ((int)($c['is_active'] ?? 1) === 0) ? '' : 'disabled' ?>>&#10003;</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="students" class="app-panel">
  <div class="row">
    <div class="col-12">
      <div class="card h-100">
        <div class="card-header bg-white fw-semibold">Alumnos registrados</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:70px">ID</th>
                  <th>Alumno</th>
                  <th style="width:130px">Grupo</th>
                  <th class="text-end" style="width:260px">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($students) === 0): ?>
                  <tr><td colspan="4" class="text-center text-muted py-4">Sin alumnos registrados.</td></tr>
                <?php endif; ?>
                <?php foreach ($students as $s): ?>
                  <tr class="<?= ((int)($s['is_active'] ?? 1) === 1) ? '' : 'table-secondary' ?>">
                    <td><?= (int)$s['id'] ?></td>
                    <td>
                      <?= htmlspecialchars(trim($s['nombre'] . ' ' . $s['apellido_paterno'] . ' ' . $s['apellido_materno'])) ?>
                      <?php if ((int)($s['is_active'] ?? 1) !== 1): ?>
                        <span class="badge text-bg-warning ms-2">Inhabilitado</span>
                      <?php endif; ?>
                    </td>
                    <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string)$s['group_code']) ?></span></td>
                    <td class="text-end">
                      <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="/students/<?= (int)$s['id'] ?>">Ver</a>
                        <a class="btn btn-sm btn-outline-secondary" href="/students/<?= (int)$s['id'] ?>/edit">Editar</a>
                        <?php if ((int)($s['is_active'] ?? 1) === 1): ?>
                          <form method="post" action="/students/<?= (int)$s['id'] ?>/delete" class="m-0" onsubmit="return confirm('¿Inhabilitar alumno?');">
                            <button class="btn btn-sm btn-outline-danger">Inhabilitar</button>
                          </form>
                        <?php else: ?>
                          <form method="post" action="/students/<?= (int)$s['id'] ?>/enable" class="m-0" onsubmit="return confirm('¿Habilitar alumno?');">
                            <button class="btn btn-sm btn-outline-success">Habilitar</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();

ob_start();
?>
<script>
(() => {
  const panels = ['register-student', 'register-group', 'catalogs', 'students'];
  const sidebar = document.getElementById('sidebar');

  const panelFromHash = () => {
    const hash = (window.location.hash || '').replace('#', '').trim();
    return panels.includes(hash) ? hash : null;
  };

  const setActiveLink = (panel) => {
    const links = document.querySelectorAll('#sidebar a[data-panel]');
    links.forEach((a) => a.classList.remove('active'));
    const active = document.querySelector(`#sidebar a[data-panel="${panel}"]`);
    if (active) active.classList.add('active');
  };

  const showPanel = (panel) => {
    panels.forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.toggle('d-none', id !== panel);
    });

    setActiveLink(panel);

    if (sidebar && window.bootstrap?.Offcanvas) {
      const offcanvas = window.bootstrap.Offcanvas.getInstance(sidebar);
      if (offcanvas) offcanvas.hide();
    }
  };

  window.addEventListener('hashchange', () => {
    showPanel(panelFromHash() ?? 'students');
  });

  document.querySelectorAll('#sidebar a[data-panel]').forEach((a) => {
    a.addEventListener('click', (e) => {
      const panel = a.getAttribute('data-panel');
      if (!panel || !panels.includes(panel)) return;
      e.preventDefault();
      window.location.hash = panel;
      showPanel(panel);
    });
  });

  showPanel(panelFromHash() ?? 'students');
})();

(() => {
  const carrera = document.getElementById('carrera');
  const turno = document.getElementById('turno');
  const grado = document.getElementById('grado');
  const code = document.getElementById('groupCode');
  const hint = document.getElementById('groupHint');
  const submit = document.getElementById('groupSubmit');

  const refresh = async () => {
    const c = carrera.value;
    const t = turno.value;
    const g = grado.value;

    code.value = '-';
    submit.disabled = true;
    if (!c || !t || !g) {
      hint.textContent = 'Selecciona carrera, turno y grado.';
      return;
    }

    hint.textContent = 'Generando...';
    try {
      const url = new URL('/groups/preview', window.location.origin);
      url.searchParams.set('carrera', c);
      url.searchParams.set('turno', t);
      url.searchParams.set('grado', g);
      const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if (!data.ok) throw new Error(data.message || 'Error');
      code.value = data.code;
      hint.textContent = 'Siguiente disponible para esa combinación.';
      submit.disabled = false;
    } catch (e) {
      hint.textContent = 'No se pudo generar el grupo.';
    }
  };

  carrera.addEventListener('change', refresh);
  turno.addEventListener('change', refresh);
  grado.addEventListener('change', refresh);

  refresh();
})();
</script>
<?php
$scripts = ob_get_clean();

echo View::render('layout', compact('title', 'content', 'scripts'));
