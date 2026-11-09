<?php
$page = 'cat'; $title = 'Edit category';
require_once '../dbconfig.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['category_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = :id");
$stmt->execute([':id' => $id]);
$cat = $stmt->fetch();
if (!$cat) { flash('That category does not exist.', 'err'); redirect('/categories/list.php'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $cat['category_name'] = $name; $cat['description'] = $desc;
    if ($name === '') {
        $error = 'Enter a category name.';
    } elseif (SECURE_MODE && has_html($name . $desc)) {
        $error = 'HTML tags and the < > characters are not allowed.';
    } else {
        $up = $pdo->prepare("UPDATE categories SET category_name = :n, description = :d, last_updated_by = :u WHERE category_id = :id");
        $up->execute([':n' => $name, ':d' => $desc, ':u' => current_user_id(), ':id' => $id]);
        flash("Category \"$name\" updated.");
        redirect('/categories/list.php');
    }
}
require '../includes/header.php';
?>
<div class="head"><div><h1>Edit category</h1><p>Changes apply to the category only. Its items stay linked.</p></div></div>
<?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <input type="hidden" name="category_id" value="<?= $id ?>">
  <label for="category_name">Category name</label>
  <input id="category_name" name="category_name" value="<?= e($cat['category_name']) ?>" maxlength="100" required>
  <label for="description">Description</label>
  <input id="description" name="description" value="<?= e($cat['description']) ?>" maxlength="255">
  <div class="actions"><button class="btn" type="submit">Save changes</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php require '../includes/footer.php'; ?>
