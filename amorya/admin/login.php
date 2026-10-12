<?php
require_once __DIR__.'/../includes/functions.php';
if(!empty($_SESSION['admin_id'])) redirect('admin/index.php');

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $email=trim((string)($_POST['email']??''));
    $pass=(string)($_POST['password']??'');
    $s=db()->prepare('SELECT id,nombre,password_hash FROM admins WHERE email=?');
    $s->execute([$email]);
    $a=$s->fetch();
    if($a && password_verify($pass,$a['password_hash'])){
        session_regenerate_id(true);
        $_SESSION['admin_id']=$a['id'];
        $_SESSION['admin_nombre']=$a['nombre'];
        redirect('admin/index.php');
    }
    $error='Correo o contraseña incorrectos.';
}
$pageTitle='Acceso administrativo';
require __DIR__.'/../includes/header.php';
?>
<section class="login-shell">
  <div class="login-decoration login-decoration-one"></div>
  <div class="login-decoration login-decoration-two"></div>

  <div class="login-card">
    <div class="login-brand">
      <span class="login-mark">A</span>
      <div><strong>AMORYA</strong><small>TIENDA VIRTUAL</small></div>
    </div>

    <div class="eyebrow">Zona protegida</div>
    <h1>Bienvenido de nuevo</h1>
    <p class="muted login-subtitle">Ingresa a tu panel para administrar productos y pedidos.</p>

    <?php if($error): ?>
      <div class="alert error login-alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="login-form" autocomplete="on">
      <?= csrf_field() ?>

      <div class="floating-field">
        <input id="login-email" type="email" name="email" autocomplete="username" placeholder=" " required>
        <label for="login-email">Correo electrónico</label>
        <span class="field-icon">✉</span>
      </div>

      <div class="floating-field">
        <input id="login-password" type="password" name="password" autocomplete="current-password" placeholder=" " required>
        <label for="login-password">Contraseña</label>
        <button type="button" class="password-toggle" data-password-toggle="#login-password" aria-label="Mostrar contraseña">◉</button>
      </div>

      <button class="login-submit" type="submit">
        <span>Iniciar sesión</span><span class="login-arrow">→</span>
      </button>
    </form>

    <a class="back-store" href="<?= e(url('index.php')) ?>">← Volver a la tienda</a>
  </div>
</section>
<?php require __DIR__.'/../includes/footer.php'; ?>
