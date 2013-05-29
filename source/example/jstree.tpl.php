<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div id="instructions">
	<h1>Using QjsTree</h1>
	
	<p>Displaying the data in a tree.</p>
</div>

<div id="demoZone">
	<p><?php $this->jsTree->Render(); ?></p>
	<p><?php $this->btnSave->Render(); ?></p>
</div>

<?php $this->RenderEnd(); ?>
<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/footer.inc.php'); ?>
