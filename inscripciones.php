<?php
require 'config.php';
$error = ''; $ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $pdo->prepare("DELETE FROM inscripciones WHERE inscripcion_id = ?")->execute([(int)$_POST['inscripcion_id']]);
        header('Location: inscripciones.php'); exit;
    }
    $club = (int)($_POST['club_id'] ?? 0);
    $miembro = (int)($_POST['miembro_id'] ?? 0);
    $fecha = $_POST['fecha_inscripcion'] ?? date('Y-m-d');
    if (!$club || !$miembro) {
        $error = 'Seleccione un club y un miembro.';
    } else {
        try {
            $pdo->prepare("INSERT INTO inscripciones (club_id, miembro_id, fecha_inscripcion) VALUES (?,?,?)")
                ->execute([$club, $miembro, $fecha]);
            $ok = 'Inscripción registrada correctamente.';
        } catch (PDOException $ex) {
            $error = ($ex->getCode() == 23000) ? 'El miembro ya está inscrito en ese club.' : 'Error al registrar.';
        }
    }
}

$clubes = $pdo->query("SELECT club_id, nombre FROM clubes ORDER BY nombre")->fetchAll();
$miembros = $pdo->query("SELECT miembro_id, nombre, apellido FROM miembros ORDER BY apellido")->fetchAll();
$lista = $pdo->query("SELECT i.inscripcion_id, c.nombre AS club, c.deporte, m.nombre, m.apellido, i.fecha_inscripcion
                      FROM inscripciones i
                      JOIN clubes c ON c.club_id = i.club_id
                      JOIN miembros m ON m.miembro_id = i.miembro_id
                      ORDER BY i.fecha_inscripcion DESC")->fetchAll();
include 'includes/header.php';
?>
<h1>Inscripciones</h1>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="ok"><?= e($ok) ?></div><?php endif; ?>
<form method="post" class="card">
  <select name="club_id" required>
    <option value="">-- Club --</option>
    <?php foreach ($clubes as $c): ?><option value="<?= e($c['club_id']) ?>"><?= e($c['nombre']) ?></option><?php endforeach; ?>
  </select>
  <select name="miembro_id" required>
    <option value="">-- Miembro --</option>
    <?php foreach ($miembros as $m): ?><option value="<?= e($m['miembro_id']) ?>"><?= e($m['apellido'] . ' ' . $m['nombre']) ?></option><?php endforeach; ?>
  </select>
  <input type="date" name="fecha_inscripcion" value="<?= date('Y-m-d') ?>" required>
  <div><button>Inscribir</button></div>
</form>

<table>
  <tr><th>Club</th><th>Deporte</th><th>Miembro</th><th>Fecha</th><th>Acción</th></tr>
  <?php foreach ($lista as $r): ?>
  <tr>
    <td><?= e($r['club']) ?></td><td><?= e($r['deporte']) ?></td>
    <td><?= e($r['apellido'] . ' ' . $r['nombre']) ?></td><td><?= e($r['fecha_inscripcion']) ?></td>
    <td class="acciones">
      <form method="post" onsubmit="return confirm('¿Eliminar inscripción?')">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="inscripcion_id" value="<?= e($r['inscripcion_id']) ?>">
        <button class="danger">Eliminar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php include 'includes/footer.php'; ?>
