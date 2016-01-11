<?php if ($exportable): ?>
<div class="panel">
  <h4>Export SPOKEdb JSON</h4>
  <div>
    <a href="<?php echo $this->url('items/export/' . metadata('item', 'id')); ?>" class="export-json big button" name="export">Export SPOKEdb JSON</a>
    <?php if ($this->visibleCheckbox) { ?>
    <label for="export_sub">
      <input type="checkbox" id="export_sub" name="export_sub" value="">
      With subordinates
    </label>
    <?php } else { ?>
      <input type="hidden" id="export_sub" name="export_sub" value="">
    <?php } ?>
  </div>
</div>

<div class="panel">
  <h4>Delete SPOKEdb JSON</h4>
  <div>
    <a href="<?php echo $this->url('items/unindex/' . metadata('item', 'id')); ?>" class="delete-json big red button" name="delete">Delete Public Record(s)</a>
    <?php if ($this->visibleCheckbox) { ?>
    <label for="delete_sub">
      <input type="checkbox" id="delete_sub" name="delete_sub" value="">
      With subordinates
    </label>
    <?php } else { ?>
      <input type="hidden" id="delete_sub" name="delete_sub" value="">
    <?php } ?>
  </div>
</div>
<?php endif; ?>
