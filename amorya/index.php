<?php
require_once __DIR__ . '/includes/functions.php';
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = db()->prepare('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.activo=1 AND (p.nombre LIKE ? OR p.descripcion LIKE ?) ORDER BY p.creado_en DESC');
    $like = '%' . $q . '%'; $stmt->execute([$like,$like]);
} else {
    $stmt = db()->query('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.activo=1 ORDER BY p.creado_en DESC');
}
$products = $stmt->fetchAll();
$pageTitle = 'Inicio';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div><div class="eyebrow">Bienvenida a AMORYA</div><h1>Detalles que hacen especial cada día.</h1><p>Encuentra tus productos favoritos en un solo lugar. Compra de forma sencilla y elige delivery o recojo.</p><a class="btn" href="#catalogo">Explorar catálogo</a></div>
  <div class="hero-art" aria-hidden="true">♡<br><span style="font-size:22px;letter-spacing:5px">HECHO PARA TI</span></div>
</section>
<div class="section-head" id="catalogo"><div><div class="eyebrow">Nuestra selección</div><h2>Catálogo de productos</h2><p class="muted">Descubre lo que tenemos para ti.</p></div>
<form class="search-form" method="get"><input name="q" value="<?= e($q) ?>" placeholder="Buscar productos..."><button>Buscar</button></form></div>
<?php if (!$products): ?><div class="empty"><div class="emoji">🛍️</div><h3>Aún no hay productos para mostrar</h3><p class="muted">Ingresa al panel de administración y registra tus primeros productos.</p><a class="btn" href="<?= e(url('admin/login.php')) ?>">Administrar tienda</a></div>
<?php else: ?><div class="products"><?php foreach($products as $p): ?><article class="product"><div class="product-image"><?php if ($p['imagen']): ?><img src="<?= e($p['imagen']) ?>" alt="<?= e($p['nombre']) ?>"><?php else: ?><span class="placeholder">♡</span><?php endif; ?></div><div class="product-body"><div class="muted" style="font-size:12px"><?= e($p['categoria'] ?? 'General') ?></div><h3><?= e($p['nombre']) ?></h3><div class="muted" style="font-size:13px"><?= e(mb_strimwidth((string)$p['descripcion'],0,75,'…')) ?></div><div class="price"><?= money($p['precio']) ?></div><?php if ((int)$p['stock']>0): ?><form method="post" action="<?= e(url('cart.php')) ?>"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><button>Agregar al carrito</button></form><?php else: ?><button disabled style="background:#aaa">Agotado</button><?php endif; ?></div></article><?php endforeach; ?></div><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>