<?php
// Shared form fields for add.php and edit.php. Expects $item, $cats.
$sizes = ['Short','Tall','Grande','Venti','Regular'];
?>
<label for="category_id">Category</label>
<select id="category_id" name="category_id" required>
  <option value="">Choose a category</option>
  <?php foreach ($cats as $c): ?>
  <option value="<?= $c['category_id'] ?>" <?= (int)$item['category_id']===(int)$c['category_id']?'selected':'' ?>><?= e($c['category_name']) ?></option>
  <?php endforeach; ?>
</select>
<label for="item_name">Item name</label>
<input id="item_name" name="item_name" value="<?= e($item['item_name']) ?>" maxlength="100" required>
<label for="price">Price (PHP)</label>
<input id="price" name="price" type="number" step="0.01" min="0" value="<?= e($item['price']) ?>" required>
<label for="size">Size</label>
<select id="size" name="size">
  <?php foreach ($sizes as $s): ?>
  <option <?= $item['size']===$s?'selected':'' ?>><?= $s ?></option>
  <?php endforeach; ?>
</select>
