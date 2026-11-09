<?php
$page = 'cat'; $title = 'Add category';
require_once '../dbconfig.php';
require_login();

$name = ''; $desc = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name === '') {
        $error = 'Enter a category name.';
    } elseif (SECURE_MODE && has_html($name . $desc)) {
        $error = 'HTML tags and the < > characters are not allowed.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name, description, added_by) VALUES (:n, :d, :u)");
        $stmt->execute([':n' => $name, ':d' => $desc, ':u' => current_user_id()]);
        flash("Category \"$name\" added.");
        redirect('/categories/list.php');
    }
}
require '../includes/header.php';
?>
<div class="head"><div><h1>Add category</h1><p>Create a parent record first, then add menu items under it.</p></div></div>
<?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <label for="category_name">Category name</label>
  <input id="category_name" name="category_name" value="<?= e($name) ?>" maxlength="100" required>
  <label for="description">Description</label>
  <input id="description" name="description" value="<?= e($desc) ?>" maxlength="255">
  <div class="actions"><button class="btn" type="submit">Save category</button><a class="btn ghost" href="list.php">Cancel</a></div>
</form>
<?php require '../includes/footer.php'; ?>
