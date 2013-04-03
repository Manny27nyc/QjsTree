<?php
	/**
	 * The base class for the QjsTree plugin.
	 * the basic functionality is implemented here.
	 */

	/**
	 * Select node event: user clicked or pressed enter on a tree node.
	 */
	class QjsTree_SelectNodeEvent extends QEvent {
		const EventName = 'select_node.jstree';
	}
	
	/**
	 * The jsTree menu plugin items enumeration.
	 * Use it to select menu items to be shown in a pop-up menu.
	 */
	class QjsTreeMenu {
		const Create = "create";
		const Rename = "rename";
		const Remove = "remove";
		const Cut    = "cut";
		const Copy   = "copy";
		const Paste  = "paste";
		const All    = "all";
	}

	/**
	 * The supported jsTree plugins enumeration.
	 * Use it in a QjsTree::AddPlugin function.
	 */
	class QjsTreePlugin {
		const themes      = "themes";
		const json_data   = "json_data";
		const ui          = "ui";
		const dnd         = "dnd";
		const crrm        = "crrm";
		const contextmenu = "contextmenu";
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
	 * @property string[] $MenuItems Menu items to be shown in a pop-up menu.
	 * Use the QjsTreeMenu enumeration members to construct the array.
	 * It is set to [QjsTreeMenu::All] by default.
	 */
	class QjsTreeBase extends QjsTreeGen {
		// TODO: support dots and icons flags (http://www.jstree.com/documentation/themes)
		protected $strTheme;
		protected $strPluginArray;
		protected $objDataSourceArray;
		protected $strMenuItemsArray;
		
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
			
			$this->strPluginArray = array(
				QjsTreePlugin::themes, QjsTreePlugin::json_data, QjsTreePlugin::ui
			);
			$this->strTheme = "default";
			$this->mixAlwaysCopy = false;
			$this->strMenuItemsArray = array(QjsTreeMenu::All);
		}

		protected function makeJqOptions() {
			$strJqOptions = parent::makeJqOptions();
			if (strlen($strJqOptions)) {
				$strJqOptions .= ',';
			}
			$strJqOptions .= $this->makeJsProperty('Plugins', 'plugins');
			if (in_array(QjsTreePlugin::json_data, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_DataSourceJs', 'json_data');
			}
			if (in_array(QjsTreePlugin::themes, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_ThemesJs', 'themes');
			}
			if (in_array(QjsTreePlugin::contextmenu, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_ContextMenuI18NJs', 'contextmenu');
			}
			if (in_array(QjsTreePlugin::crrm, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_CRRMJs', 'crrm');
			}
			if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
			return $strJqOptions;
		}
		
		public function GetControlJavaScript() {
			$strToReturn =
				sprintf('jQuery("#%s").%s({%s})'
					, $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions());

			if (in_array(QjsTreePlugin::crrm, $this->strPluginArray)) {
				if (in_array(QjsTreeMenu::All  , $this->strMenuItemsArray) ||
					in_array(QjsTreeMenu::Cut  , $this->strMenuItemsArray) ||
					in_array(QjsTreeMenu::Copy , $this->strMenuItemsArray) ||
					in_array(QjsTreeMenu::Paste, $this->strMenuItemsArray)
				) {
					$strToReturn .=
						sprintf('.on("move_node.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
							, $this->getJqControlId(), $this->getJqControlId());
				}
				if (in_array(QjsTreeMenu::All, $this->strMenuItemsArray) || in_array(QjsTreeMenu::Rename, $this->strMenuItemsArray)) {
					$strToReturn .=
						sprintf('.on("rename.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
							, $this->getJqControlId(), $this->getJqControlId());
				}
				if (in_array(QjsTreeMenu::All, $this->strMenuItemsArray) || in_array(QjsTreeMenu::Remove, $this->strMenuItemsArray)) {
					$strToReturn .=
						sprintf('.on("remove.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
							, $this->getJqControlId(), $this->getJqControlId());
				}
				if (in_array(QjsTreeMenu::All, $this->strMenuItemsArray) || in_array(QjsTreeMenu::Create, $this->strMenuItemsArray)) {
					$strToReturn .=
						sprintf('.on("create.jstree", function (e, data) {qcubed.recordControlModification("%s", "_DataJson", JSON.stringify(jQuery("#%s").jstree("get_json", -1)))})'
							, $this->getJqControlId(), $this->getJqControlId());
				}
			}
			return $strToReturn;
		}

		/**
		 * Add a plugin to be used with this tree. 
		 * @param string $strPlugin The name of the plugin to be added
		 */
		public function AddPlugin($strPlugin) {
			if (!in_array($strPlugin, $this->strPluginArray)) {
				$this->strPluginArray[] = $strPlugin;
			}
		}

		public function __get($strName) {
			switch ($strName) {
				case "Plugins" :
					return $this->strPluginArray;
				case "DataSource" :
					return $this->objDataSourceArray;
				case "Theme" :
					return $this->strTheme;
				case "MenuItems" :
					return $this->strMenuItemsArray;
				case "AlwaysCopy" :
					return $this->mixAlwaysCopy;

				// For internal use only.
				case "_DataSourceJs" :
					return array("data" => $this->objDataSourceArray);
				case "_ThemesJs" :
					return array(
						"theme" => $this->strTheme
						, "url" => __PLUGIN_ASSETS__ . "/QjsTree/jstree-v.pre1.0/themes/" . $this->strTheme . "/style.css"
					);
				case "_CRRMJs" :
					return array(
						"move" => array(
							"always_copy" => $this->mixAlwaysCopy
						)
					);
				case "_ContextMenuI18NJs" :
					$items = array (
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
					);
					if (1 == count($this->strMenuItemsArray) && "all" == $this->strMenuItemsArray[0]) {
						return array("items" => $items);
					}
					$items2 = array();
					if (in_array(QjsTreeMenu::Create, $this->strMenuItemsArray)) {
						$items2[QjsTreeMenu::Create] = $items[QjsTreeMenu::Create];
					} else {
						$items2[QjsTreeMenu::Create] = false;
					}
					if (in_array(QjsTreeMenu::Rename, $this->strMenuItemsArray)) {
						$items2[QjsTreeMenu::Rename] = $items[QjsTreeMenu::Rename];
					} else {
						$items2[QjsTreeMenu::Rename] = false;
					}
					if (in_array(QjsTreeMenu::Remove, $this->strMenuItemsArray)) {
						$items2[QjsTreeMenu::Remove] = $items[QjsTreeMenu::Remove];
					} else {
						$items2[QjsTreeMenu::Remove] = false;
					}
					$ccp_submenu = array();
					if (in_array(QjsTreeMenu::Cut, $this->strMenuItemsArray)) {
						$ccp_submenu[QjsTreeMenu::Cut] = $items["ccp"]["submenu"][QjsTreeMenu::Cut];
					} else {
						$ccp_submenu[QjsTreeMenu::Cut] = false;
					}
					if (in_array(QjsTreeMenu::Copy, $this->strMenuItemsArray)) {
						$ccp_submenu[QjsTreeMenu::Copy] = $items["ccp"]["submenu"][QjsTreeMenu::Copy];
					} else {
						$ccp_submenu[QjsTreeMenu::Copy] = false;
					}
					if (in_array(QjsTreeMenu::Paste, $this->strMenuItemsArray)) {
						$ccp_submenu[QjsTreeMenu::Paste] = $items["ccp"]["submenu"][QjsTreeMenu::Paste];
					} else {
						$ccp_submenu[QjsTreeMenu::Paste] = false;
					}
					if (count($ccp_submenu)) {
						$items2["ccp"] = $items["ccp"];
						$items2["ccp"]["submenu"] = $ccp_submenu;
					}
					return array(
						"items" => $items2
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
						$this->strPluginArray = QType::Cast($mixValue, QType::ArrayType);
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
					
				case 'MenuItems':
					try {
						$this->strMenuItemsArray = QType::Cast($mixValue, QType::ArrayType);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					
				// For internal use only.
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
