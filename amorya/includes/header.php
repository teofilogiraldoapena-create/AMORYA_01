<?php $pageTitle = $pageTitle ?? APP_NAME; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | AMORYA</title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
  <a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark">A</span><span>AMORYA<small>TIENDA VIRTUAL</small></span></a>
  <nav>
    <a href="<?= e(url('index.php')) ?>">Inicio</a>
    <a href="<?= e(url('index.php#catalogo')) ?>">Catálogo</a>
    <a class="cart-link" href="<?= e(url('cart.php')) ?>">Carrito <span><?= cart_count() ?></span></a>
    <a href="<?= e(url('admin/login.php')) ?>">Administración</a>
  </nav>
</header>
<main class="container">
<?= flash_html() ?>
