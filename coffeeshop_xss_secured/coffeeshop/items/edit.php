<?php
$page = 'item'; $title = 'Edit menu item';
require_once '../dbconfig.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['item_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE item_id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();
if (!$item) { flash('That menu item does not exist.', 'err'); redirect('/items/list.php'); }
$cats = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = [
        'item_id'     => $id,
        'category_id' => (int)($_POST['category_id'] ?? 0),
        'item_name'   => trim($_POST['item_name'] ?? ''),
        'price'       => $_POST['price'] ?? '',
        'size'        => $_POST['size'] ?? 'Regular',
    ];
    if (!$item['category_id'] || $item['item_name'] === '' || !is_numeric($item['price']) || $item['price'] < 0) {
        $error = 'Choose a category, enter an item name, and enter a valid price.';
    } elseif (SECURE_MODE && has_html($item['item_name'])) {
        $error = 'HTML tags and the < > characters are not allowed.';
    } else {
        $up = $pdo->prepare("UPDATE menu_items SET category_id=:c, item_name=:n, price=:p, size=:s, last_updated_by=:u WHERE item_id=:id");
        $up->execute([
            ':c' => $item['category_id'], ':n' => $item['item_name'],
            ':p' => $item['price'], ':s' => $item['size'],
            ':u' => current_user_id(), ':id' => $id,
        ]);
        flash("Menu item \"{$item['item_name']}\" updated.");
        redirect('/items/list.php');
    }
}
require '../includes/header.php';
?>
<div class="head"><div><h1>Edit menu item</h1><p>Change the category to move this item to a different parent.</p></div></div>
<?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <input type="hidden" name="item_id" value="<?= $id ?>">
  <?php require '_form.php'; ?>
  <div class="actions"><button class="btn" type="submit">Save changes</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php require '../includes/footer.php'; ?>
