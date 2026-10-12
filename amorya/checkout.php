<?php
require_once __DIR__ . '/includes/functions.php';
$items=cart_products();
if(!$items){set_flash('Agrega productos antes de finalizar el pedido.','error');redirect('cart.php');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $nombre=trim((string)($_POST['nombre']??''));$email=trim((string)($_POST['email']??''));$telefono=trim((string)($_POST['telefono']??''));$tipo=$_POST['tipo_entrega']??'delivery';$direccion=trim((string)($_POST['direccion']??''));$pago=$_POST['metodo_pago']??'yape';$notas=trim((string)($_POST['notas']??''));
 $validTypes=['delivery','recojo'];$validPayments=['yape','plin','transferencia','efectivo','tarjeta'];
 if($nombre===''||$telefono===''||!in_array($tipo,$validTypes,true)||!in_array($pago,$validPayments,true)||($tipo==='delivery'&&$direccion==='')||($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL)))$error='Completa correctamente los campos obligatorios.';
 else {
  try{
   $pdo=db();$pdo->beginTransaction();$subtotal=0;$fresh=[];
   foreach($items as $it){$s=$pdo->prepare('SELECT id,nombre,precio,stock,activo FROM productos WHERE id=? FOR UPDATE');$s->execute([$it['id']]);$p=$s->fetch();$qty=(int)$it['cantidad'];if(!$p||!(int)$p['activo']||(int)$p['stock']<$qty)throw new RuntimeException('Uno de los productos ya no tiene stock suficiente. Regresa al carrito.');$line=(float)$p['precio']*$qty;$subtotal+=$line;$fresh[]=['p'=>$p,'qty'=>$qty,'line'=>$line];}
   $delivery=($tipo==='delivery')?0.00:0.00; // Configura aquí una tarifa real de delivery cuando la definas.
   $total=$subtotal+$delivery;$codigo='AMO'.date('ymd').strtoupper(bin2hex(random_bytes(3)));
   $s=$pdo->prepare('INSERT INTO pedidos(codigo,cliente_nombre,cliente_email,cliente_telefono,tipo_entrega,direccion,metodo_pago,notas,subtotal,costo_delivery,total) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
   $s->execute([$codigo,$nombre,$email?:null,$telefono,$tipo,$tipo==='delivery'?$direccion:null,$pago,$notas?:null,$subtotal,$delivery,$total]);$pedidoId=(int)$pdo->lastInsertId();
   $d=$pdo->prepare('INSERT INTO pedido_detalles(pedido_id,producto_id,nombre_producto,precio_unitario,cantidad,total_linea) VALUES(?,?,?,?,?,?)');
   foreach($fresh as $row)$d->execute([$pedidoId,$row['p']['id'],$row['p']['nombre'],$row['p']['precio'],$row['qty'],$row['line']]);
   $pdo->commit();$_SESSION['cart']=[];$_SESSION['last_order']=['codigo'=>$codigo,'total'=>$total,'metodo'=>$pago,'tipo'=>$tipo];redirect('pedido_exito.php');
  }catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();$error=$ex instanceof RuntimeException?$ex->getMessage():'No se pudo registrar el pedido. Verifica la base de datos e inténtalo de nuevo.';}
 }
}
$subtotal=array_sum(array_column($items,'total_linea'));$pageTitle='Finalizar pedido';require __DIR__.'/includes/header.php';
?>
<h1>Finalizar pedido</h1><p class="muted">Completa tus datos para registrar tu compra en AMORYA.</p>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="grid-two"><form class="form-card" method="post"><?= csrf_field() ?><h2>Datos del cliente</h2><label>Nombre completo *</label><input name="nombre" required value="<?= e($_POST['nombre']??'') ?>"><div class="form-row"><div><label>Teléfono *</label><input name="telefono" required value="<?= e($_POST['telefono']??'') ?>"></div><div><label>Correo electrónico</label><input type="email" name="email" value="<?= e($_POST['email']??'') ?>"></div></div><label>Tipo de entrega *</label><select name="tipo_entrega" data-delivery-type><option value="delivery">Delivery a domicilio</option><option value="recojo" <?= (($_POST['tipo_entrega']??'')==='recojo')?'selected':'' ?>>Recojo en tienda</option></select><div data-address-wrap><label>Dirección de entrega *</label><textarea name="direccion" rows="3" placeholder="Distrito, calle, referencia..."><?= e($_POST['direccion']??'') ?></textarea></div><label>Método de pago *</label><select name="metodo_pago"><?php foreach(['yape'=>'Yape','plin'=>'Plin','transferencia'=>'Transferencia bancaria','efectivo'=>'Efectivo (coordinar)','tarjeta'=>'Tarjeta (requiere integración de pasarela)'] as $v=>$label): ?><option value="<?= e($v) ?>" <?= (($_POST['metodo_pago']??'yape')===$v)?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select><label>Notas adicionales</label><textarea name="notas" rows="3"><?= e($_POST['notas']??'') ?></textarea><p class="muted">El pedido queda pendiente hasta que la tienda confirme el pago y la disponibilidad.</p><button style="width:100%">Registrar pedido</button></form><aside class="summary"><h2>Tu pedido</h2><?php foreach($items as $it): ?><p><?= (int)$it['cantidad'] ?> × <?= e($it['nombre']) ?><strong style="float:right"><?= money($it['total_linea']) ?></strong></p><?php endforeach; ?><hr><p>Subtotal <strong style="float:right"><?= money($subtotal) ?></strong></p><p>Delivery <strong style="float:right">Por confirmar</strong></p><p class="total"><?= money($subtotal) ?>*</p><small class="muted">* El total registrado inicialmente no incluye tarifa de delivery.</small></aside></div>
<?php require __DIR__.'/includes/footer.php'; ?>