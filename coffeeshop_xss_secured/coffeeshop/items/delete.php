<?php
$page = 'item'; $title = 'Delete menu item';
require_once '../dbconfig.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['item_id'] ?? 0);

$stmt = $pdo->prepare("SELECT m.*, c.category_name FROM menu_items m JOIN categories c ON c.category_id = m.category_id WHERE m.item_id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();
if (!$item) { flash('That menu item does not exist.', 'err'); redirect('/items/list.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $pdo->prepare("DELETE FROM menu_items WHERE item_id = :id");
    $del->execute([':id' => $id]);
    flash("Menu item \"{$item['item_name']}\" deleted.");
    redirect('/items/list.php');
}
require '../includes/header.php';
?>
<div class="head"><div><h1>Delete menu item</h1></div></div>
<form class="card" method="post">
  <input type="hidden" name="item_id" value="<?= $id ?>">
  <p>You are about to delete <strong><?= e($item['item_name']) ?></strong> (<?= e($item['size']) ?>, &#8369;<?= number_format($item['price'],2) ?>) from <strong><?= e($item['category_name']) ?></strong>.</p>
  <div class="actions"><button class="btn danger" type="submit">Delete menu item</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php require '../includes/footer.php'; ?>
