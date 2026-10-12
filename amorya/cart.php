<?php
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action==='add') {
        $id=(int)($_POST['product_id']??0);
        $st=db()->prepare('SELECT id,stock,activo FROM productos WHERE id=?');$st->execute([$id]);$p=$st->fetch();
        if($p && (int)$p['activo'] && (int)$p['stock']>0){$_SESSION['cart'][$id]=min((int)($_SESSION['cart'][$id]??0)+1,(int)$p['stock']);set_flash('Producto agregado al carrito.');}
        else set_flash('El producto no está disponible.','error');
    } elseif($action==='update') {
        foreach(($_POST['qty']??[]) as $id=>$qty){$id=(int)$id;$qty=(int)$qty;if($qty<=0){unset($_SESSION['cart'][$id]);continue;}$st=db()->prepare('SELECT stock,activo FROM productos WHERE id=?');$st->execute([$id]);$p=$st->fetch();if(!$p||!(int)$p['activo'])unset($_SESSION['cart'][$id]);else $_SESSION['cart'][$id]=min($qty,(int)$p['stock']);}
        set_flash('Carrito actualizado.');
    } elseif($action==='remove') {unset($_SESSION['cart'][(int)($_POST['product_id']??0)]);set_flash('Producto retirado del carrito.');}
    redirect('cart.php');
}
$items=cart_products();$subtotal=array_sum(array_column($items,'total_linea'));
$pageTitle='Mi carrito';require __DIR__.'/includes/header.php';
?>
<h1>Mi carrito</h1><p class="muted">Revisa tus productos antes de confirmar el pedido.</p>
<?php if(!$items): ?><div class="empty"><div class="emoji">🛒</div><h2>Tu carrito está vacío</h2><p>Agrega productos del catálogo para continuar.</p><a class="btn" href="<?= e(url('index.php')) ?>">Ver catálogo</a></div>
<?php else: ?><div class="grid-two"><section class="panel"><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="update"><div class="table-wrap"><table><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th><th></th></tr></thead><tbody><?php foreach($items as $it): ?><tr><td><strong><?= e($it['nombre']) ?></strong><div class="muted">Stock: <?= (int)$it['stock'] ?></div></td><td><?= money($it['precio']) ?></td><td><input class="qty" type="number" min="0" max="<?= (int)$it['stock'] ?>" name="qty[<?= (int)$it['id'] ?>]" value="<?= (int)$it['cantidad'] ?>"></td><td><?= money($it['total_linea']) ?></td><td><button class="secondary" form="remove-<?= (int)$it['id'] ?>">Quitar</button></td></tr><?php endforeach; ?></tbody></table></div><p><button class="secondary">Actualizar cantidades</button></p></form><?php foreach($items as $it): ?><form id="remove-<?= (int)$it['id'] ?>" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>"></form><?php endforeach; ?></section><aside class="summary"><h2>Resumen</h2><p>Subtotal <strong style="float:right"><?= money($subtotal) ?></strong></p><p class="muted">El costo de delivery, si corresponde, se confirmará durante el pedido.</p><hr><p class="total"><?= money($subtotal) ?></p><a class="btn" style="width:100%" href="<?= e(url('checkout.php')) ?>">Continuar al pedido</a><p><a href="<?= e(url('index.php')) ?>">← Seguir comprando</a></p></aside></div><?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>