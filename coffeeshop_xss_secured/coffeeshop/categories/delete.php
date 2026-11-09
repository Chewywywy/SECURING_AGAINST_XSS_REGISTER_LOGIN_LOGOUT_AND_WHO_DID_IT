<?php
$page = 'cat'; $title = 'Delete category';
require_once '../dbconfig.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['category_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT c.*, COUNT(m.item_id) AS item_count
    FROM categories c LEFT JOIN menu_items m ON m.category_id = c.category_id
    WHERE c.category_id = :id GROUP BY c.category_id");
$stmt->execute([':id' => $id]);
$cat = $stmt->fetch();
if (!$cat) { flash('That category does not exist.', 'err'); redirect('/categories/list.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ON DELETE CASCADE removes the child menu items automatically
    $del = $pdo->prepare("DELETE FROM categories WHERE category_id = :id");
    $del->execute([':id' => $id]);
    flash("Category \"{$cat['category_name']}\" deleted along with {$cat['item_count']} item(s).");
    redirect('/categories/list.php');
}

$items = $pdo->prepare("SELECT item_name FROM menu_items WHERE category_id = :id ORDER BY item_name");
$items->execute([':id' => $id]);
$items = $items->fetchAll(PDO::FETCH_COLUMN);
require '../includes/header.php';
?>
<div class="head"><div><h1>Delete category</h1></div></div>
<form class="card" method="post">
  <input type="hidden" name="category_id" value="<?= $id ?>">
  <p>You are about to delete <strong><?= e($cat['category_name']) ?></strong>.</p>
  <?php if ($items): ?>
  <div class="warn">
    This also deletes its <?= count($items) ?> menu item(s): <?= e(implode(', ', $items)) ?>.
  </div>
  <?php else: ?><p>This category has no menu items.</p><?php endif; ?>
  <div class="actions"><button class="btn danger" type="submit">Delete category</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php require '../includes/footer.php'; ?>
