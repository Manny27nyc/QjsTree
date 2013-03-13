<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QjsTree */
		protected $jsTree;
		
		/** @var QButton */
		protected $btnSave;

		protected function Form_Create() {
			// Define the tree
			$this->jsTree = $this->jsTree_Create($this);
			
			$this->btnSave = new QButton($this);
			$this->btnSave->Text = QApplication::Translate("Save");
			$this->btnSave->AddAction(new QClickEvent, new QAjaxAction('btnSave_Click'));
		}
		
		protected function jsTree_Create($objParentControl, $strControlId = null) {
			// Define the tree
			$this->jsTree = new QjsTree($objParentControl, $strControlId);
			$this->jsTree->AlwaysCopy = "multitree";
			$this->jsTree->DataSource = array(
				array(
					"data" => "A node"
					, "children" => array("Child 1", "A Child 2")
				)
				, array(
					"data" => "Long format demo"
				)
			);
			$this->jsTree->AddPlugin("dnd");
			$this->jsTree->AddPlugin("crrm");
			$this->jsTree->AddPlugin("contextmenu");
			
			$this->jsTree->AddAction(new QjsTree_SelectNodeEvent, new QAjaxAction("jsTree_Click"));
			$this->jsTree->ActionParameter = new QJsClosure('return jQuery.data(ui.rslt.obj[0], "id")');
			
			return $this->jsTree;
		}
		
		protected function jsTree_Click($strFormId, $strControlId, $strParameter) {
			QApplication::DisplayAlert($strParameter);
		}
		
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			$strJsonData = json_encode($this->jsTree->DataSource);
			QApplication::DisplayAlert($strJsonData);
		}
	}

	ExampleForm::Run('ExampleForm');
?>