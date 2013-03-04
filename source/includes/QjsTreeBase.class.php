<?php

	/**
	 */
	class QjsTreeBase extends QjsTreeGen {
		protected $strTheme = "default";

		public function  __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->AddJavascriptFile("../../plugins/QjsTree/jstree-v.pre1.0/jquery.jstree.js");
			$this->AddCssFile("../../plugins/QjsTree/jstree-v.pre1.0/themes/" . $this->strTheme . "/style.css");
		}

		public function __get($strName) {
			switch ($strName) {
				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}
		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {

				default:
					try {
						parent::__set($strName, $mixValue);
						break;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				}
		}

	}

?>
