<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QjsTree */
		protected $jsTree;

		protected function Form_Create() {
			// Define the DataGrid
			$this->jsTree = new QjsTree($this);
			$this->jsTree->DataSource = array(
				array(
					"data" => "A node"
					, "children" => array("Child 1", "A Child 2")
				)
				, array(
					"data" => "Long format demo"
				)
			);
		}
	}

	ExampleForm::Run('ExampleForm');
?>