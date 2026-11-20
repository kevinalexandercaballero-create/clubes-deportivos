<?php
require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $pdo->prepare("DELETE FROM clubes WHERE club_id = ?")->execute([(int)$_POST['club_id']]);
        header('Location: clubes.php'); exit;
    }
    $nombre = trim($_POST['nombre'] ?? '');
    $deporte = trim($_POST['deporte'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $fecha = $_POST['fecha_fundacion'] ?? '';
    if ($nombre === '' || $deporte === '' || $ubicacion === '' || $fecha === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($fecha > date('Y-m-d')) {
        $error = 'La fecha de fundación no puede ser futura.';
    } else {
        if (!empty($_POST['club_id'])) {
            $pdo->prepare("UPDATE clubes SET nombre=?, deporte=?, ubicacion=?, fecha_fundacion=? WHERE club_id=?")
                ->execute([$nombre, $deporte, $ubicacion, $fecha, (int)$_POST['club_id']]);
        } else {
            $pdo->prepare("INSERT INTO clubes (nombre, deporte, ubicacion, fecha_fundacion) VALUES (?,?,?,?)")
                ->execute([$nombre, $deporte, $ubicacion, $fecha]);
        }
        header('Location: clubes.php'); exit;
    }
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare("SELECT * FROM clubes WHERE club_id = ?");
    $st->execute([(int)$_GET['editar']]);
    $editar = $st->fetch();
}
$clubes = $pdo->query("SELECT * FROM clubes ORDER BY nombre")->fetchAll();
include 'includes/header.php';
?>
<h1>Clubes</h1>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <input type="hidden" name="accion" value="guardar">
  <input type="hidden" name="club_id" value="<?= e($editar['club_id'] ?? '') ?>">
  <input name="nombre" placeholder="Nombre del club" value="<?= e($editar['nombre'] ?? '') ?>" required>
  <input name="deporte" placeholder="Deporte" value="<?= e($editar['deporte'] ?? '') ?>" required>
  <input name="ubicacion" placeholder="Ubicación" value="<?= e($editar['ubicacion'] ?? '') ?>" required>
  <input type="date" name="fecha_fundacion" max="<?= date('Y-m-d') ?>" value="<?= e($editar['fecha_fundacion'] ?? '') ?>" required>
  <div><button><?= $editar ? 'Actualizar' : 'Registrar' ?> club</button>
  <?php if ($editar): ?><a class="btn" href="clubes.php">Cancelar</a><?php endif; ?></div>
</form>

<table>
  <tr><th>ID</th><th>Nombre</th><th>Deporte</th><th>Ubicación</th><th>Fundación</th><th>Acciones</th></tr>
  <?php foreach ($clubes as $c): ?>
  <tr>
    <td><?= e($c['club_id']) ?></td><td><?= e($c['nombre']) ?></td><td><?= e($c['deporte']) ?></td>
    <td><?= e($c['ubicacion']) ?></td><td><?= e($c['fecha_fundacion']) ?></td>
    <td class="acciones">
      <a class="btn" href="?editar=<?= e($c['club_id']) ?>">Editar</a>
      <form method="post" onsubmit="return confirm('¿Eliminar este club?')">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="club_id" value="<?= e($c['club_id']) ?>">
        <button class="danger">Eliminar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php include 'includes/footer.php'; ?>
