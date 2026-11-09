<?php
$page = 'item'; $title = 'Add menu item';
require_once '../dbconfig.php';
require_login();

$cats = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();
$item = ['category_id' => (int)($_GET['category'] ?? 0), 'item_name' => '', 'price' => '', 'size' => 'Grande'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = [
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
        $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, item_name, price, size, added_by) VALUES (:c, :n, :p, :s, :u)");
        $stmt->execute([
            ':c' => $item['category_id'], ':n' => $item['item_name'],
            ':p' => $item['price'], ':s' => $item['size'], ':u' => current_user_id(),
        ]);
        flash("Menu item \"{$item['item_name']}\" added.");
        redirect('/items/list.php');
    }
}
require '../includes/header.php';
?>
<div class="head"><div><h1>Add menu item</h1><p>Every item must belong to a category.</p></div></div>
<?php if (!$cats): ?>
  <div class="flash err">There are no categories yet. <a href="<?= BASE_URL ?>/categories/add.php">Add a category</a> first.</div>
<?php else: ?>
<?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <?php require '_form.php'; ?>
  <div class="actions"><button class="btn" type="submit">Save menu item</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php endif; ?>
<?php require '../includes/footer.php'; ?>
