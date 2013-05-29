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
	 * Check node event: user checked the checkbox. The checkbox plugin should be enabled for that.
	 */
	class QjsTree_CheckNodeEvent extends QEvent {
		const EventName = 'check_node.jstree';
	}
	
	/**
	 * Uncheck node event: user unchecked the checkbox. The checkbox plugin should be enabled for that.
	 */
	class QjsTree_UncheckNodeEvent extends QEvent {
		const EventName = 'uncheck_node.jstree';
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
		const checkbox = "checkbox";
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
	 * 
	 * @property boolean $OverrideUi Checkbox plugin property. Default is false. If set to true all selection will be handled by checkboxes. The checkbox plugin will map UI's get_selected function to its own get_checked function and overwrite the UI reselect function. It will also disable the select_node, deselect_node and deselect_all functions. If left as false nodes can be selected and checked independently.
	 * @property boolean $CheckedParentOpen Checkbox plugin property. Default is true. When set to true when programatically checking a node in the tree all of its closed parents are opened automatically.
	 * @property boolean $TwoState Checkbox plugin property. Default is false. If set to true checkboxes will be two-state only, meaning that you will be able to select parent and children independently and there will be no undetermined state.
	 * @property boolean $RealCheckboxes Checkbox plugin property. Default is false. If set to true real hidden checkboxes will be created for each element, so if the tree is part of a form, checked nodes will be submitted automatically. By default the name of the checkbox is "check_" + the ID of the LI element and the value is 1, this can be changed using the real_checkboxes_names config option.
	 * @property string $RealCheckboxesNames Checkbox plugin property. A function. Default is function (n) { return [("check_" + (n[0].id || Math.ceil(Math.random() * 10000))), 1]; }.
	 * If real checkboxes are used this function is invoked in the current tree's scope for each new checkbox that is created. It receives a single argument - the node that will contain the checkbox. The function must return an array consisting of two values - the name for the checkbox and the value for the checkbox.
	 * @property int[] $CheckedIndexes The array of indexes of nodes that are currently checked.
	 * 
	 * @property int $SelectLimit Ui plugin property. Default is -1. Defines how many nodes can be selected at a given time (-1 means unlimited).
	 * @property string $SelectMultipleModifier Ui plugin property. Default is "ctrl". The special key used to make a click add to the selection and not replace it ("ctrl", "shift", "alt", "meta").
	 * You can also set this to "on" making every click add to the selection.
	 * @property string $SelectRangeModifier Ui plugin property. Default is "shift". The special key used to make a click expand a range from the last selected item ("ctrl", "shift", "alt", "meta").
	 * Note that the last clicked elemtn should be a sibling of the currently clicked element so that a range will be created (same as common file explorers).
	 * @property string $SelectedParentClose Ui plugin property. A string (or false). Default is "select_parent". What action to take when a selected node's parent is closed (making the selected node invisible). Possible values are false - do nothing, "select_parent" - select the closed node's parent and "deselect" - deselect the node.
	 * @property string $SelectedParentOpen Ui plugin property. Default is true. When set to true when programatically selecting a node in the tree all of its closed parents are opened automatically.
	 * @property string $SelectPrevOnDelete Ui plugin property. Default is true. If set to true when a selected node is deleted, its previous sibling (or parent) is selected.
	 * @property string $DisableSelectingChildren Ui plugin property. Default is false. If set to true you will not be able to select children of already selected nodes.
	 * @property int[] $SelectedIndexes The array of indexes of nodes that are currently selected.
	 * 
	 * @property-read int $ItemCount The overall number of items in the tree.
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

		// checkbox plugin properties (http://www.jstree.com/documentation/checkbox)
		protected $blnOverrideUi;
		protected $blnCheckedParentOpen;
		protected $blnTwoState;
		protected $blnRealCheckboxes;
		protected $strRealCheckboxesNames;
		protected $intCheckedArray;
		
		// ui plugin properties (http://www.jstree.com/documentation/ui)
		protected $intSelectLimit;
		protected $strSelectMultipleModifier;
		protected $strSelectRangeModifier;
		protected $strSelectedParentClose;
		protected $strSelectedParentOpen;
		protected $strSelectPrevOnDelete;
		protected $strDisableSelectingChildren;
		// initially_select - not supported. use SelectedIndexes instead.
		protected $intSelectedArray;
		
		public function  __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->AddJavascriptFile("../../plugins/QjsTree/jstree-v.pre1.0/jquery.jstree.js");
			$this->AddJavascriptFile("../../plugins/QjsTree/jstree.util.js");
			
			$this->strPluginArray = array(
				QjsTreePlugin::themes, QjsTreePlugin::json_data, QjsTreePlugin::ui
			);
			$this->strTheme = "default";
			$this->mixAlwaysCopy = false;
			$this->strMenuItemsArray = array(QjsTreeMenu::All);
			
			// checkbox plugin properties (http://www.jstree.com/documentation/checkbox)
			$this->blnOverrideUi = false;
			$this->blnCheckedParentOpen = true;
			$this->blnTwoState = false;
			$this->blnRealCheckboxes = false;
			$this->strRealCheckboxesNames = null;
			
			// ui plugin properties (http://www.jstree.com/documentation/ui)
			$this->intSelectLimit = -1;
			$this->strSelectMultipleModifier = "ctrl";
			$this->strSelectRangeModifier = "shift";
			$this->strSelectedParentClose = "select_parent";
			$this->strSelectedParentOpen = true;
			$this->strSelectPrevOnDelete = true;
			$this->strDisableSelectingChildren = false;
			$this->intSelectedArray = array();
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
			if (in_array(QjsTreePlugin::checkbox, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_CheckboxJs', 'checkbox');
			}
			if (in_array(QjsTreePlugin::ui, $this->strPluginArray)) {
				$strJqOptions .= $this->makeJsProperty('_UiJs', 'ui');
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
			if (in_array(QjsTreePlugin::checkbox, $this->strPluginArray)) {
				// Check on load, if needed.
				if ($this->CheckedIndexes && count($this->CheckedIndexes)) {
					$strToReturn .=
						sprintf('.on("loaded.jstree", function (e, data) {var checked_array = %s; jQuery("#%s").jstree("get_unchecked", null, true).each (function(){ var id = jQuery.data(this, "id"); if (-1 !== jQuery.inArray(id, checked_array)) { jQuery("#%s").jstree("check_node", this); } });})'
							, json_encode($this->CheckedIndexes)
							, $this->getJqControlId()
							, $this->getJqControlId()
							, $this->getJqControlId());
				}
				// Collect checked nodes collection.
				$strToReturn .=
					sprintf('.on("change_state.jstree", function (e, data) {var checked_array = []; jQuery("#%s").jstree("get_checked", null, true).each (function(){ checked_array.push(jQuery.data(this, "id"));}); qcubed.recordControlModification("%s", "_CheckedDataJson", JSON.stringify(checked_array))})'
						, $this->getJqControlId(), $this->getJqControlId());
			}
			if (in_array(QjsTreePlugin::ui, $this->strPluginArray)) {
				// Select on load, if needed.
				if ($this->SelectedIndexes && count($this->SelectedIndexes)) {
					$strToReturn .=
						sprintf('.on("loaded.jstree", function (e, data) {var selected_array = %s; visit_all_nodes("#%s", function(node){ var id = jQuery.data(node, "id"); if (-1 !== jQuery.inArray(id, selected_array)) { jQuery("#%s").jstree("select_node", node); } });})'
							, json_encode($this->SelectedIndexes)
							, $this->getJqControlId()
							, $this->getJqControlId()
							, $this->getJqControlId());
				}
				// Collect selected nodes collection.
				$strToReturn .=
					sprintf('.on("select_node.jstree", function (e, data) {var selected_array = []; jQuery("#%s").jstree("get_selected", null).each (function(){ selected_array.push(jQuery.data(this, "id"));}); qcubed.recordControlModification("%s", "_SelectedDataJson", JSON.stringify(selected_array))})'
						, $this->getJqControlId(), $this->getJqControlId());
				$strToReturn .=
					sprintf('.on("deselect_node.jstree", function (e, data) {var selected_array = []; jQuery("#%s").jstree("get_selected", null).each (function(){ selected_array.push(jQuery.data(this, "id"));}); qcubed.recordControlModification("%s", "_SelectedDataJson", JSON.stringify(selected_array))})'
						, $this->getJqControlId(), $this->getJqControlId());
				$strToReturn .=
					sprintf('.on("deselect_all.jstree", function (e, data) {var selected_array = []; jQuery("#%s").jstree("get_selected", null).each (function(){ selected_array.push(jQuery.data(this, "id"));}); qcubed.recordControlModification("%s", "_SelectedDataJson", JSON.stringify(selected_array))})'
						, $this->getJqControlId(), $this->getJqControlId());
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
		
		protected static function calcItemCount($objDataArray) {
			$intItemCount = 0;
			foreach ($objDataArray as $objData) {
				$intItemCount++;
				if (isset($objData['children'])) {
					$intItemCount += self::calcItemCount($objData['children']);
				}
			}
			return $intItemCount;
		}

		public function __get($strName) {
			switch ($strName) {
				case "Plugins" :
					return $this->strPluginArray;
				case "DataSource" :
					return $this->objDataSourceArray;
				case "ItemCount" :
					return self::calcItemCount($this->objDataSourceArray);
				case "CheckedIndexes" :
					return $this->intCheckedArray;
				case "Theme" :
					return $this->strTheme;
				case "MenuItems" :
					return $this->strMenuItemsArray;
				case "AlwaysCopy" :
					return $this->mixAlwaysCopy;

				// checkbox plugin properties
				case "OverrideUi" :
					return $this->blnOverrideUi;
				case "CheckedParentOpen" :
					return $this->blnCheckedParentOpen;
				case "TwoState" :
					return $this->blnTwoState;
				case "RealCheckboxes" :
					return $this->blnRealCheckboxes;
				case "RealCheckboxesNames" :
					return $this->strRealCheckboxesNames;

				// ui plugin properties (http://www.jstree.com/documentation/ui)
				case "SelectLimit" :
					return $this->intSelectLimit;
				case "SelectMultipleModifier" :
					return $this->strSelectMultipleModifier;
				case "SelectRangeModifier" :
					return $this->strSelectRangeModifier;
				case "SelectedParentClose" :
					return $this->strSelectedParentClose;
				case "SelectedParentOpen" :
					return $this->strSelectedParentOpen;
				case "SelectPrevOnDelete" :
					return $this->strSelectPrevOnDelete;
				case "DisableSelectingChildren" :
					return $this->strDisableSelectingChildren;
				case "SelectedIndexes" :
					return $this->intSelectedArray;

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
				case "_CheckboxJs" :
					$strProperties = array();
					if ($this->blnOverrideUi) {
						$strProperties["override_ui"] = $this->blnOverrideUi;
					}
					if (!$this->blnCheckedParentOpen) {
						$strProperties["checked_parent_open"] = $this->blnCheckedParentOpen;
					}
					if ($this->blnTwoState) {
						$strProperties["two_state"] = $this->blnTwoState;
					}
					if ($this->blnRealCheckboxes) {
						$strProperties["real_checkboxes"] = $this->blnRealCheckboxes;
					}
					if ($this->strRealCheckboxesNames) {
						$strProperties["real_checkboxes_names"] = $this->strRealCheckboxesNames;
					}
					return $strProperties;
				case "_UiJs" :
					$strProperties = array();
//					if (-1 !== $this->intSelectLimit) {
						$strProperties["select_limit"] = $this->intSelectLimit;
//					}
					if ("ctrl" !== $this->strSelectMultipleModifier) {
						$strProperties["select_multiple_modifier"] = $this->strSelectMultipleModifier;
					}
					if ("shift" !== $this->strSelectRangeModifier) {
						$strProperties["select_range_modifier"] = $this->strSelectRangeModifier;
					}
					if ("select_parent" !== $this->strSelectedParentClose) {
						$strProperties["selected_parent_close"] = $this->strSelectedParentClose;
					}
					if (true !== $this->strSelectedParentOpen) {
						$strProperties["selected_parent_open"] = $this->strSelectedParentOpen;
					}
					if (true !== $this->strSelectPrevOnDelete) {
						$strProperties["select_prev_on_delete"] = $this->strSelectPrevOnDelete;
					}
					if (false !== $this->strDisableSelectingChildren) {
						$strProperties["disable_selecting_children"] = $this->strDisableSelectingChildren;
					}
					return $strProperties;
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

				case 'CheckedIndexes':
					try {
						$this->intCheckedArray = QType::Cast($mixValue, QType::ArrayType);
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
					
				// checkbox plugin properties
				case "OverrideUi" :
					try {
						$this->blnOverrideUi = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "CheckedParentOpen" :
					try {
						$this->blnCheckedParentOpen = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "TwoState" :
					try {
						$this->blnTwoState = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "RealCheckboxes" :
					try {
						$this->blnRealCheckboxes = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "RealCheckboxesNames" :
					try {
						$this->strRealCheckboxesNames = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				// ui plugin properties (http://www.jstree.com/documentation/ui)
				case "SelectLimit" :
					try {
						$this->intSelectLimit = QType::Cast($mixValue, QType::Integer);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "SelectMultipleModifier" :
					try {
						$this->strSelectMultipleModifier = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "SelectRangeModifier" :
					try {
						$this->strSelectRangeModifier = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "SelectedParentClose" :
					try {
						$this->strSelectedParentClose = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "SelectedParentOpen" :
					try {
						$this->strSelectedParentOpen = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "SelectPrevOnDelete" :
					try {
						$this->strSelectPrevOnDelete = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "DisableSelectingChildren" :
					try {
						$this->strDisableSelectingChildren = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case 'SelectedIndexes':
					try {
						$this->intSelectedArray = QType::Cast($mixValue, QType::ArrayType);
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

				case "_CheckedDataJson" :
					try {
						$this->intCheckedArray = json_decode(QType::Cast($mixValue, QType::String), false);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "_SelectedDataJson" :
					try {
						$this->intSelectedArray = json_decode(QType::Cast($mixValue, QType::String), false);
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
