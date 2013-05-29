<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QjsTree */
		protected $jsTree;
		
		/** @var QJqButton */
		protected $btnSave;

		protected function Form_Create() {
			// Define the DataGrid
			$this->jsTree = new QjsTree($this);
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
			$this->btnSave = new QJqButton($this);
			$this->btnSave->Text = QApplication::Translate("Show selected");
			$this->btnSave->AddAction(new QClickEvent, new QAjaxAction('btnSave_Click'));
		}
		
		protected function btnSave_Click () {
			QApplication::DisplayAlert(implode(", ", $this->jsTree->SelectedIndexes));
		}
	}

	ExampleForm::Run('ExampleForm');
?>