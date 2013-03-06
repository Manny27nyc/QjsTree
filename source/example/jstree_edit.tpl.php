<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div id="instructions">
	<h1>Using QjsTree to edit the tree.</h1>
	
	<strong>Note that this example works only with jquery version 1.8 and above.</strong>
	<p>You can drag and drop items and right-click them to see a context menu.</p>
</div>

<div id="demoZone">
	<p><?php $this->jsTree->Render(); ?></p>
	<p><?php $this->btnSave->Render(); ?></p>
</div>

<?php $this->RenderEnd(); ?>
<?php require(__DOCROOT__ . __EXAMPLES__ . '/includes/footer.inc.php'); ?>
