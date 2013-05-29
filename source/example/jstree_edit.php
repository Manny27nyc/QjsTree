<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QjsTree */
		protected $jsTree;
		
		/** @var QJqButton */
		protected $btnSave;

		protected function Form_Create() {
			// Define the tree
			$this->jsTree = $this->jsTree_Create($this);
			
			$this->btnSave = new QJqButton($this);
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
					, "metadata" => array(
						"id" => 1
					)
					, "children" => array(
						array(
							"data" => "Child 1"
							, "metadata" => array(
								"id" => 3
							)
						)
						, array(
							"data" => "A Child 2"
							, "metadata" => array(
								"id" => 4
							)
						)
					)
				)
				, array(
					"data" => "Long format demo"
					, "metadata" => array(
						"id" => 2
					)
				)
			);
			$this->jsTree->AddPlugin(QjsTreePlugin::dnd);
			$this->jsTree->AddPlugin(QjsTreePlugin::crrm);
			$this->jsTree->AddPlugin(QjsTreePlugin::contextmenu);
			$this->jsTree->AddPlugin(QjsTreePlugin::checkbox);
			
			$this->jsTree->AddAction(new QjsTree_SelectNodeEvent, new QAjaxAction("jsTree_Click"));
			$this->jsTree->ActionParameter = new QJsClosure('return jQuery.data(ui.rslt.obj[0], "id")');
			
			$this->jsTree->AddAction(new QjsTree_CheckNodeEvent, new QAjaxAction("jsTree_Check"));
			// need to set the ActionParameter only once
//			$this->jsTree->ActionParameter = new QJsClosure('return jQuery.data(ui.rslt.obj[0], "id")');
			$this->jsTree->AddAction(new QjsTree_UncheckNodeEvent, new QAjaxAction("jsTree_Uncheck"));
			// need to set the ActionParameter only once
//			$this->jsTree->ActionParameter = new QJsClosure('return jQuery.data(ui.rslt.obj[0], "id")');
			
			$this->jsTree->CheckedIndexes = array(2, 4);
			
			return $this->jsTree;
		}
		
		protected function jsTree_Click($strFormId, $strControlId, $strParameter) {
			QApplication::DisplayAlert($strParameter);
		}

		protected function jsTree_Check($strFormId, $strControlId, $strParameter) {
			QApplication::DisplayAlert($strParameter);
		}

		protected function jsTree_Uncheck($strFormId, $strControlId, $strParameter) {
			QApplication::DisplayAlert($strParameter);
		}

		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			$strJsonData = json_encode($this->jsTree->DataSource);
			QApplication::DisplayAlert($strJsonData);
		}
	}

	ExampleForm::Run('ExampleForm');
?>