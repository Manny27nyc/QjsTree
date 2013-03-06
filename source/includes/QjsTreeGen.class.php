<?php
	/**
	 * The abstract QjsTreeGen class 
	 */

	/* Custom event classes for this control */
	
	

	/* Custom "property" event classes for this control */

	/**
	 * 
	 */

	class QjsTreeGen extends QPanel	{
		protected $strJavaScripts = __JQUERY_EFFECTS__;
		protected $strStyleSheets = __JQUERY_CSS__;
		
		/**
		 * Converts the given PHP-property value in the textual representation of
		 * the javascript-property given.
		 * @param string $strProp PHP-property
		 * @param string $strKey javascript-property
		 * @return string
		 */
		protected function makeJsProperty($strProp, $strKey) {
			$objValue = $this->$strProp;
			if (null === $objValue) {
				return '';
			}

			return $strKey . ': ' . JavaScriptHelper::toJsObject($objValue) . ', ';
		}

		protected function makeJqOptions() {
			$strJqOptions = '';
//			$strJqOptions .= $this->makeJsProperty('ServerMethod1', 'sServerMethod');
//			if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
			return $strJqOptions;
		}

		public function getJqControlId() {
			return $this->ControlId;
		}

		public function getJqSetupFunction() {
			return 'jstree';
		}

		public function GetControlJavaScript() {
			return
				sprintf('jQuery("#%s").%s({%s})'
					. '.bind("move_node.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
					. '.bind("rename.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
					. '.bind("remove.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
					. '.bind("create.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
					, $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions()
					, $this->getJqControlId(), $this->getJqControlId()
					, $this->getJqControlId(), $this->getJqControlId()
					, $this->getJqControlId(), $this->getJqControlId()
					, $this->getJqControlId(), $this->getJqControlId());
		}

		public function GetEndScript() {
			return  $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
		}
		
		/**
		 * Call a JQuery UI Method on the object. Takes variable number of arguments.
		 * 
		 * @param string $strMethodName the method name to call
		 * @internal param $mixed [optional] $mixParam1
		 * @internal param $mixed [optional] $mixParam2
		 */
		protected function CallJqUiMethod($strMethodName /*, ... */) {
			$args = func_get_args();

			$strArgs = JavaScriptHelper::toJsObject($args);
			$strJs = sprintf('jQuery("#%s").%s(%s)',
				$this->getJqControlId(),
				$this->getJqSetupFunction(),
				substr($strArgs, 1, strlen($strArgs)-2));	// params without brackets
			QApplication::ExecuteJavaScript($strJs);
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
//				case 'ServerMethod1':
//					try {
//						$this->strServerMethod1 = QType::Cast($mixValue, QType::String);
//						if ($this->Rendered) {
//							$this->CallJqUiMethod("option", $strName, $mixValue);
//						}
//						break;
//					} catch (QInvalidCastException $objExc) {
//						$objExc->IncrementOffset();
//						throw $objExc;
//					}


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
