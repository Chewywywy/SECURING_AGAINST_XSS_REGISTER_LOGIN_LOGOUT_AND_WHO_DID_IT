<?php
$page = 'item'; $title = 'Menu items';
require '../includes/header.php';
require_login();

$filter = (int)($_GET['category'] ?? 0);
$cats = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();

$sql = "SELECT m.*, c.category_name, au.username AS added_by_name, uu.username AS updated_by_name
        FROM menu_items m
        JOIN categories c ON c.category_id = m.category_id
        LEFT JOIN users au ON au.user_id = m.added_by
        LEFT JOIN users uu ON uu.user_id = m.last_updated_by";
$params = [];
if ($filter) { $sql .= " WHERE m.category_id = :c"; $params[':c'] = $filter; }
$sql .= " ORDER BY c.category_name, m.item_name";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$items = $stmt->fetchAll();
?>
<div class="head">
  <div><h1>Menu items</h1><p>The child records. Each item belongs to exactly one category.</p></div>
  <div class="filter">
    <form method="get" class="filter">
      <select name="category" aria-label="Filter by category" data-autosubmit>
        <option value="0">All categories</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?= $c['category_id'] ?>" <?= $filter==$c['category_id']?'selected':'' ?>><?= e($c['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <a class="btn" href="add.php">Add menu item</a>
  </div>
</div>
<div class="panel">
<?php if (!$items): ?><div class="empty">No menu items found. Add one to fill the menu.</div><?php else: ?>
<table>
  <tr><th>ID</th><th>Item</th><th>Category</th><th>Size</th><th class="num">Price</th><th>Added by</th><th>Last updated</th><th></th></tr>
  <?php foreach ($items as $i): ?>
  <tr>
    <td><?= $i['item_id'] ?></td>
    <td><strong><?= e($i['item_name']) ?></strong></td>
    <td><span class="badge"><?= e($i['category_name']) ?></span></td>
    <td><?= e($i['size']) ?></td>
    <td class="num price">&#8369;<?= number_format($i['price'],2) ?></td>
    <td class="meta"><?= e($i['added_by_name'] ?? '—') ?></td>
    <td class="meta">
      <?= $i['updated_by_name'] ? e($i['updated_by_name']) . ' · ' : '' ?>
      <?= $i['last_updated'] ? e(date('M j, Y g:i A', strtotime($i['last_updated']))) : '—' ?>
    </td>
    <td class="act">
      <a class="btn ghost sm" href="edit.php?id=<?= $i['item_id'] ?>">Edit</a>
      <a class="btn danger sm" href="delete.php?id=<?= $i['item_id'] ?>">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>
