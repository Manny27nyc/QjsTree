<?php
	/**
	 * The base class for the QjsTree plugin.
	 * the basic functionality is implemented here.
	 */

	/**
	 * Blur event: keyboard focus moving away from the control.
	 */
	class QjsTree_SelectNodeEvent extends QEvent {
		const EventName = 'select_node.jstree';
	}

	/**
	 * The base class for the QjsTree plugin.
	 * the basic functionality is implemented here.
	 * 
	 * @property string $Theme The theme used to shoe this tree. Possible values: default, default-rtl, classic, apple. The default is default.
	 * @property string[] $Plugins The plugins used by this jsTree instance. The ["themes", "json_data", "ui"] are the default.
	 * @property mixed[] $DataSource The data displayed in the tree. The format is described here: http://www.jstree.com/documentation/json_data
	 * @property bool|string $AlwaysCopy true, false or "multitree". Default is false.
	 * Defines how moves are handled - if set to true every move will be forced to a copy
	 * (leaving the original node in place).
	 * If set to "multitree" only moves between trees will be forced to a copy.
	 */
	class QjsTreeBase extends QjsTreeGen {
		// TODO: support dots and icons flags (http://www.jstree.com/documentation/themes)
		protected $strTheme;
		protected $strPlugins;
		protected $objDataSourceArray;

		// crrm plugin (http://www.jstree.com/documentation/crrm)
		
		/**
		 * Defines how moves are handled - if set to true every move will be forced to a copy
		 * (leaving the original node in place).
		 * If set to "multitree" only moves between trees will be forced to a copy.
		 * @var bool|string true, false or "multitree". Default is false. 
		 */
		protected $mixAlwaysCopy;

		public function  __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->AddJavascriptFile("../../plugins/QjsTree/jstree-v.pre1.0/jquery.jstree.js");
			
			$this->strPlugins = array(
				"themes", "json_data", "ui"
			);
			$this->strTheme = "default";
			$this->mixAlwaysCopy = false;
		}

		protected function makeJqOptions() {
			$strJqOptions = parent::makeJqOptions();
			if (strlen($strJqOptions)) {
				$strJqOptions .= ',';
			}
			$strJqOptions .= $this->makeJsProperty('Plugins', 'plugins');
			$strJqOptions .= $this->makeJsProperty('_DataSourceJs', 'json_data');
			$strJqOptions .= $this->makeJsProperty('_ThemesJs', 'themes');
			$strJqOptions .= $this->makeJsProperty('_ContextMenuI18NJs', 'contextmenu');
			$strJqOptions .= $this->makeJsProperty('_CRRMJs', 'crrm');
			if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
			return $strJqOptions;
		}
		
		/**
		 * Add a plugin to be used with this tree. 
		 * @param string $strPlugin The name of the plugin to be added
		 */
		public function AddPlugin($strPlugin) {
			if (!in_array($strPlugin, $this->strPlugins)) {
				$this->strPlugins[] = $strPlugin;
			}
		}

		public function __get($strName) {
			switch ($strName) {
				case "Plugins" :
					return $this->strPlugins;
				case "DataSource" :
					return $this->objDataSourceArray;
				// For internal use only.
				case "_DataSourceJs" :
					return array("data" => $this->objDataSourceArray);
				case "Theme" :
					return $this->strTheme;
				case "_ThemesJs" :
					return array(
						"theme" => $this->strTheme
						, "url" => __PLUGIN_ASSETS__ . "/QjsTree/jstree-v.pre1.0/themes/" . $this->strTheme . "/style.css"
					);
				case "AlwaysCopy" :
					return $this->mixAlwaysCopy;
				case "_CRRMJs" :
					return array(
						"move" => array(
							"always_copy" => $this->mixAlwaysCopy
						)
					);
				case "_ContextMenuI18NJs" :
					return array(
						"items" => array(
							"create" => array(
								"separator_before"	=> false,
								"separator_after"	=> true,
								"label"				=> QApplication::Translate("Create"),
								"action"			=> new QJsClosure("this.create(obj)", array("obj"))
							)
							, "rename" => array(
								"separator_before"	=> false,
								"separator_after"	=> false,
								"label"				=> QApplication::Translate("Rename"),
								"action"			=> new QJsClosure("this.rename(obj)", array("obj"))
							)
							, "remove" => array(
								"separator_before"	=> false,
								"separator_after"	=> false,
								"icon"				=> false,
								"label"				=> QApplication::Translate("Delete"),
								"action"			=> new QJsClosure("if(this.is_selected(obj)) { this.remove(); } else { this.remove(obj); }", array("obj"))
							)
							, "ccp" => array(
								"separator_before"	=> true,
								"separator_after"	=> false,
								"icon"				=> false,
								"label"				=> QApplication::Translate("Edit"),
								"action"			=> false,
								"submenu"			=> array(
									"cut" => array(
										"separator_before"	=> false,
										"separator_after"	=> false,
										"label"				=> QApplication::Translate("Cut"),
										"action"			=> new QJsClosure("this.cut(obj)", array("obj"))
									)
									, "copy" => array(
										"separator_before"	=> false,
										"separator_after"	=> false,
										"icon"				=> false,
										"label"				=> QApplication::Translate("Copy"),
										"action"			=> new QJsClosure("this.copy(obj)", array("obj"))
									)
									, "paste" => array(
										"separator_before"	=> false,
										"separator_after"	=> false,
										"icon"				=> false,
										"label"				=> QApplication::Translate("Paste"),
										"action"			=> new QJsClosure("this.paste(obj)", array("obj"))
									)
								)
							)
						)
					);
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
				case 'Plugins':
					try {
						$this->strPlugins = QType::Cast($mixValue, QType::ArrayType);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'DataSource':
					try {
						$this->objDataSourceArray = QType::Cast($mixValue, QType::ArrayType);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Theme':
					try {
						$this->strTheme = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					
				case 'AlwaysCopy':
					try {
						if (!is_string($mixValue)) {
							$this->mixAlwaysCopy = QType::Cast($mixValue, QType::Boolean);
						} else {
							$this->mixAlwaysCopy = QType::Cast($mixValue, QType::String);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					
				case "_DataJson" :
					try {
						$this->objDataSourceArray = json_decode(QType::Cast($mixValue, QType::String), true);
						// We decode data as an associative array. If the original object was empty,
						// On an encode conversion it would be converted as not an object, but array.
						// This breaks the javascript code, that want it to be an object.
						// Let's remove all empty arrays to avoid this problem.
						self::cleanupEmptyArrays($this->objDataSourceArray);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

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

		protected static function cleanupEmptyArrays(&$arr) {
			foreach ($arr as $key => &$value) {
				if (!is_array($value)) {
					continue;
				}
				if (!count($value)) {
					unset($arr[$key]);
					continue;
				}
				self::cleanupEmptyArrays($arr[$key]);
			}
		}

	}

?>
