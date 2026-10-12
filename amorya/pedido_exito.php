<?php
require_once __DIR__.'/includes/functions.php';
$order=$_SESSION['last_order']??null;unset($_SESSION['last_order']);$pageTitle='Pedido registrado';require __DIR__.'/includes/header.php';
?>
<div class="empty"><div class="emoji">♡</div><h1>¡Gracias por tu compra!</h1><?php if($order): ?><p>Tu pedido fue registrado correctamente.</p><h2><?= e($order['codigo']) ?></h2><p class="total"><?= money($order['total']) ?></p><p>Método de pago: <strong><?= e(strtoupper($order['metodo'])) ?></strong></p><p>Entrega: <strong><?= e($order['tipo']==='delivery'?'Delivery':'Recojo en tienda') ?></strong></p><p class="muted">El equipo de AMORYA debe confirmar el pedido y coordinar el pago/entrega.</p><?php else: ?><p>No hay un pedido reciente para mostrar.</p><?php endif; ?><a class="btn" href="<?= e(url('index.php')) ?>">Volver a la tienda</a></div>
<?php require __DIR__.'/includes/footer.php'; ?>