<?php
require_once 'dbconfig.php';
require_login(); // not logged in? bounced to login.php

$page = 'home'; $title = 'Menu overview';
require 'includes/header.php';

// One query: every category with its items (LEFT JOIN keeps empty categories)
$rows = $pdo->query("
    SELECT c.category_id, c.category_name, c.description,
           m.item_id, m.item_name, m.price, m.size
    FROM categories c
    LEFT JOIN menu_items m ON m.category_id = c.category_id
    ORDER BY c.category_name, m.item_name
")->fetchAll();

$groups = [];
foreach ($rows as $r) {
    $id = $r['category_id'];
    if (!isset($groups[$id])) $groups[$id] = ['name'=>$r['category_name'], 'desc'=>$r['description'], 'items'=>[]];
    if ($r['item_id']) $groups[$id]['items'][] = $r;
}
?>
<div class="head">
  <div><h1>Menu overview</h1><p>Each category (parent) with all of its menu items (children).</p></div>
  <div><a class="btn ghost" href="<?= BASE_URL ?>/categories/add.php">Add category</a>
       <a class="btn" href="<?= BASE_URL ?>/items/add.php">Add menu item</a></div>
</div>

<?php if (!$groups): ?>
  <div class="panel"><div class="empty">No categories yet. Add your first category to start the menu.</div></div>
<?php endif; ?>

<?php foreach ($groups as $g): ?>
<section class="group panel">
  <div class="gh"><h3><?= e($g['name']) ?></h3><small><?= count($g['items']) ?> item(s)</small></div>
  <?php if ($g['items']): ?>
  <table>
    <tr><th>Item</th><th>Size</th><th class="num">Price</th></tr>
    <?php foreach ($g['items'] as $i): ?>
    <tr><td><?= e($i['item_name']) ?></td><td><?= e($i['size']) ?></td><td class="num price">&#8369;<?= number_format($i['price'],2) ?></td></tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><div class="empty">No items in this category yet.</div><?php endif; ?>
</section>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
