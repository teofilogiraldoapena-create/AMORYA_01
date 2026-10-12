<?php
require_once __DIR__.'/includes/functions.php';
if((int)db()->query('SELECT COUNT(*) FROM admins')->fetchColumn()>0){http_response_code(403);exit('Ya existe un administrador. Elimina setup_admin.php y entra por /admin/login.php.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$nombre=trim((string)($_POST['nombre']??''));$email=trim((string)($_POST['email']??''));$pass=(string)($_POST['password']??'');if($nombre===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<12)$error='Completa los datos. La contraseña debe tener al menos 12 caracteres.';else{$s=db()->prepare('INSERT INTO admins(nombre,email,password_hash) VALUES(?,?,?)');$s->execute([$nombre,$email,password_hash($pass,PASSWORD_DEFAULT)]);header('Location: '.url('admin/login.php'));exit;}}
$pageTitle='Crear administrador';require __DIR__.'/includes/header.php';
?>
<div style="max-width:560px;margin:25px auto" class="form-card"><h1>Crear administrador de AMORYA</h1><p class="muted">Este instalador solo funciona si todavía no hay administradores.</p><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><label>Nombre</label><input name="nombre" required><label>Correo de acceso</label><input type="email" name="email" required><label>Contraseña (mínimo 12 caracteres)</label><input type="password" name="password" minlength="12" required><p><button>Crear administrador</button></p></form></div>
<?php require __DIR__.'/includes/footer.php'; ?>