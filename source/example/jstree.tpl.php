<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div class="instructions">
	<h1 class="instruction_title">Using QjsTree</h1>
</div>
<div style="margin-left: 100px">
	<?php $this->jsTree->Render(); ?>
</div>

<?php $this->RenderEnd(); ?>
<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/footer.inc.php'); ?>
