<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money(float|string $value): string {
    return 'S/ ' . number_format((float)$value, 2, '.', ',');
}
function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}
function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void {
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Solicitud no válida. Recarga la página e inténtalo nuevamente.');
    }
}
function admin_required(): void {
    if (empty($_SESSION['admin_id'])) redirect('admin/login.php');
}
function cart_count(): int {
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}
function cart_products(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_values(array_filter(array_map('intval', array_keys($cart)), fn($id) => $id > 0));
    if (!$ids) return [];
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT id,nombre,precio,stock,imagen,activo FROM productos WHERE id IN ($marks)");
    $stmt->execute($ids);
    $items = [];
    foreach ($stmt->fetchAll() as $p) {
        if (!(int)$p['activo']) continue;
        $qty = max(1, min((int)($cart[$p['id']] ?? 1), (int)$p['stock']));
        $p['cantidad'] = $qty;
        $p['total_linea'] = $qty * (float)$p['precio'];
        $items[] = $p;
    }
    return $items;
}
function set_flash(string $message, string $type = 'success'): void {
    $_SESSION['_flash'] = ['message' => $message, 'type' => $type];
}
function flash_html(): string {
    if (empty($_SESSION['_flash'])) return '';
    $f = $_SESSION['_flash']; unset($_SESSION['_flash']);
    return '<div class="alert ' . e($f['type']) . '">' . e($f['message']) . '</div>';
}
