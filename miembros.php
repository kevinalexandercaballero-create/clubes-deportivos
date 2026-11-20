<?php
require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $pdo->prepare("DELETE FROM miembros WHERE miembro_id = ?")->execute([(int)$_POST['miembro_id']]);
        header('Location: miembros.php'); exit;
    }
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $id = (int)($_POST['miembro_id'] ?? 0);

    if ($nombre === '' || $apellido === '' || $email === '') {
        $error = 'Nombre, apellido y email son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } elseif ($telefono !== '' && !preg_match('/^[0-9+\- ]{7,20}$/', $telefono)) {
        $error = 'El teléfono no es válido.';
    } else {
        try {
            if ($id) {
                $pdo->prepare("UPDATE miembros SET nombre=?, apellido=?, email=?, telefono=? WHERE miembro_id=?")
                    ->execute([$nombre, $apellido, $email, $telefono, $id]);
            } else {
                $pdo->prepare("INSERT INTO miembros (nombre, apellido, email, telefono) VALUES (?,?,?,?)")
                    ->execute([$nombre, $apellido, $email, $telefono]);
            }
            header('Location: miembros.php'); exit;
        } catch (PDOException $ex) {
            $error = ($ex->getCode() == 23000) ? 'Ya existe un miembro con ese email.' : 'Error al guardar.';
        }
    }
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare("SELECT * FROM miembros WHERE miembro_id = ?");
    $st->execute([(int)$_GET['editar']]);
    $editar = $st->fetch();
}
$miembros = $pdo->query("SELECT * FROM miembros ORDER BY apellido, nombre")->fetchAll();
include 'includes/header.php';
?>
<h1>Miembros</h1>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="card">
  <input type="hidden" name="accion" value="guardar">
  <input type="hidden" name="miembro_id" value="<?= e($editar['miembro_id'] ?? '') ?>">
  <input name="nombre" placeholder="Nombre" value="<?= e($editar['nombre'] ?? '') ?>" required>
  <input name="apellido" placeholder="Apellido" value="<?= e($editar['apellido'] ?? '') ?>" required>
  <input type="email" name="email" placeholder="Email" value="<?= e($editar['email'] ?? '') ?>" required>
  <input name="telefono" placeholder="Teléfono" value="<?= e($editar['telefono'] ?? '') ?>">
  <div><button><?= $editar ? 'Actualizar' : 'Registrar' ?> miembro</button>
  <?php if ($editar): ?><a class="btn" href="miembros.php">Cancelar</a><?php endif; ?></div>
</form>

<table>
  <tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Teléfono</th><th>Acciones</th></tr>
  <?php foreach ($miembros as $m): ?>
  <tr>
    <td><?= e($m['miembro_id']) ?></td><td><?= e($m['nombre']) ?></td><td><?= e($m['apellido']) ?></td>
    <td><?= e($m['email']) ?></td><td><?= e($m['telefono']) ?></td>
    <td class="acciones">
      <a class="btn" href="?editar=<?= e($m['miembro_id']) ?>">Editar</a>
      <form method="post" onsubmit="return confirm('¿Eliminar este miembro?')">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="miembro_id" value="<?= e($m['miembro_id']) ?>">
        <button class="danger">Eliminar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php include 'includes/footer.php'; ?>
