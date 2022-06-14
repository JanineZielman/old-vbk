<?php

/*
  Content Management System

  Copyright (C) 2006?2008 Systemantics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



// Report only running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR | E_PARSE);



require_once('config.inc.php');
require_once('page.inc.php');
require_once('content.inc.php');
require_once('lang.inc.php');



define('SCRIPTNAME', 'content.php');



class cms {


	/**
	 * @var config
	 */
	var $config = null;

	/**
	 * @var content
	 */
	var $content = null;

	/**
	 * @var lang
	 */
	var $lang = null;

	/**
	 * @var page
	 */
	var $page = null;

	var $uid = null;

	var $actions = array();
	var $actionPaths = array();
	var $actionsByList = array();

	/**
	 * Ensures that the specified parameters have been submitted
	 * to the script.
	 *
	 * @param string $params
	 */
	function _getParam($param, $optional = false) {
		if (!isset($_REQUEST[$param])) {
			if ($optional) {
				return false;
			} else {
				print sprintf($this->lang->l('missingrequiredparameter'), $param);
				exit;
			}
		}
		return $_REQUEST[$param];
	}


	function _makePath($path, $defaultPath) {
		if (!$path) {
			$path = $defaultPath;
		}
		if (substr($path, -1)!='/') {
			$path .= '/';
		}
		return $path;
	}


	function _formatField($text) {
		if ($text=='') {
			return $this->lang->l('emptylabel');
		} else {
			$i = strpos($text, '</p>');
			$j = strpos($text, '</h');
			$i = min($i===false ? strlen($text) : $i, $j===false ? strlen($text) : $j);
			$newText = str_replace(array("\r", "\n"), '', strip_tags(substr($text, 0, $i)));
			return $newText.(strlen($newText)<strlen($text) ? '...' : '');
		}
	}


	function _getFieldDisplayValue($field, $atom) {
		// Determine field type
		$fieldType = false;
		foreach ($this->content->getAtomFields($atom->_type) as $atomField) {
			if ($atomField->_name==$field) {
				$fieldType = $atomField->_type;
				break;
			}
		}
		// Get field contents
		if ($field=='_label') {
			$fieldValue = $this->_formatField($atom->_label);
		} else if ($fieldType==FIELD_TYPE_STRING
			|| $fieldType==FIELD_TYPE_TEXT
			|| $fieldType==FIELD_TYPE_PLAINTEXT) {
			$fieldValue = $this->_formatField($atom->{$this->_extendByLanguage($field, $this->content->getDefaultLanguage())});
		} else if ($fieldType==FIELD_TYPE_CHARACTER
			|| $fieldType==FIELD_TYPE_INTEGER) {
			$fieldValue = $this->_formatField($atom->{$field});
		} else if ($fieldType==FIELD_TYPE_DATE) {
			$date = explode('-', $atom->{$field});
			$fieldValue = str_replace(
				array(
					'Y',
					'M',
					'D',
				),
				array(
					$date[0],
					$date[1],
					$date[2],
				),
				$this->lang->l('dateformat')
			);
		} else if ($fieldType==FIELD_TYPE_COLOR) {
			$fieldValue = '<div class="colorbox" style="background-color:#'.$atom->{$field}.';"></div>';
		} else if ($fieldType==FIELD_TYPE_LINK || $fieldType==FIELD_TYPE_MULTILINK) {
			$fieldValue = '';
			foreach (explode(',', $atom->{$field}) as $atomId) {
				$linkedAtom = $this->content->getAtom($atomId);
				if ($linkedAtom) {
					$fieldValue .= ', '.$this->_formatField($linkedAtom->_label);
				}
			}
			$fieldValue = substr($fieldValue, 2);
		} else if ($fieldType==FIELD_TYPE_IMAGE) {
			$fieldValue = $atom->{$field} ? '<img class="listPicture" src="'.PATH_PREFIX.$atomField->_params['path'].$atom->{$field}.'" />' : '';
		} else if ($fieldType==FIELD_TYPE_FILE) {
			$fieldValue = $atom->{$field} ? '<a href="'.PATH_PREFIX.$atomField->_params['path'].rawurlencode($atom->{$field}).'">'.$atom->{$field}.'</a>' : '';
		} else if ($fieldType==FIELD_TYPE_ENUM) {
			$fieldValue = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field.'.'.$atom->{$field});
		} else if ($fieldType==FIELD_TYPE_BOOLEAN) {
			$fieldValue = $this->lang->l($atom->{$field} ? 'valueyes' : 'valueno');
		} else {
			// Cannot display this value
			$fieldValue = '<em>'.$this->lang->l('valuenotdisplayable').'</em>';
		}
		return $fieldValue;
	}


	function _extendByLanguage($name, $lang) {
		return $name.($lang=='' ? '' : '_'.$lang);
	}


	function _getTargetAtomsList($targetList) {
		$options = array();
		foreach ($this->content->getAtoms(SITE_ID, $this->content->getListInfo(SITE, $targetList)) as $target) {
			$options[$target->_id] = $this->_formatField($target->_label);
		}
		asort($options);
		return $options;
	}


	function _getTargetAtomsAll($type) {
		$options = array();
		foreach ($this->content->getAllAtoms($type) as $target) {
			$options[$target->_id] = $this->_formatField($target->_label);
		}
		asort($options);
		return $options;
	}

	/**
	 * Shows a list of atoms. The list which is shown is specified
	 * by POST data.
	 */
	function showListPOST() {
		$ownerId = $this->_getParam(PARAM_OWNER);
		$listName = $this->_getParam(PARAM_LIST);

		// Get the owning atom
		$owner = $this->content->getAtom($ownerId);
		if (!$owner) {
			echo $this->lang->l('error.nosuchatom');
			exit;
		}

		// Get the list
		$listInfo = $this->content->getListInfo($owner->_type, $listName);
		if (!$listInfo) {
			echo $this->lang->l('error.nosuchlist');
			exit;
		}

		// Open the page
		$head = sprintf($this->lang->l('editlist'), ($owner->_id==SITE_ID ? $this->lang->l(LANG_PREFIX_LIST.$listName) : sprintf($this->lang->l('childof'), $this->lang->l(LANG_PREFIX_LIST.$listName), $this->_formatField($owner->_label))));
		$this->page->open($head, $head);

		print "<div id=\"container\">\n";

		if (!$this->rootId) {
			$this->_printNavigation();
		}
		print '<div id="page';
		if ($this->rootId) {
			echo ' popup';
		}
		print '">';

		// Check the access rights
		if (!$this->content->canAccess($owner, $listInfo->_name, $this->getUsername())) {
			// Access not allowed
			echo '<p>'.$this->lang->l('error.accessnotallowed')."</p>\n";
			print '</div>';
			print '</div>';
			$this->page->close();
			return true;
		}

		// Determine the list filtering
		$showFilter = trim($this->_getParam(PARAM_FILTER, true));
		$showValue = trim($this->_getParam(PARAM_VALUE, true));
		$filterCondition = $showFilter!='' ? "`$showFilter`='$showValue'" : false;

		// Determine the list's display parameters
		$totalAtomCount = $this->content->getAtomCount($owner, $listInfo, $filterCondition);
		$showIndex = intval($this->_getParam(PARAM_INDEX, true));
		if ($showIndex<0 || $showIndex>$totalAtomCount-1) {
			$showIndex = 0;
		}
		$showAmount = intval($this->_getParam(PARAM_AMOUNT, true));
		if ($showAmount<=0) {
			$showAmount = DEFAULT_AMOUNT;
		}

?><script language="JavaScript" type="text/javascript">
var handled = false;
function editEntry(url) {
	if (handled) {
		handled = true;
	} else {
		location.href = url;
	}
}
function deleteEntry(id, caption) {
	if (confirm("<?php print sprintf($this->lang->l('deletionconfirmfirst'), '"+caption+"'); ?>")
		&& confirm("<?php print sprintf($this->lang->l('deletionconfirmsecond'), '"+caption+"'); ?>")) {
		location.href= "<?php print $this->page->link(array(PARAM_ACTION=>ACTION_DELETE, PARAM_ID=>'"+id+"'), true); ?>";
	}
	handled = true;
}
</script>
<?php

		// Print back and new buttons
		$createTitle = ucfirst(sprintf($this->lang->l('createatom'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom')));
		$listButtonsSingle =
			'<a class="cmsButton" href="'.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>$owner->_id, PARAM_ID=>ID_NEW, PARAM_LIST=>$listInfo->_name, PARAM_ORDER=>1)).'" title="'.$createTitle.'"><img src="elements/new.gif" alt="'.$createTitle.'"></a>';
		if ($listInfo->_ordering==LIST_ORDERING_DEFAULT) {
			$listButtonsMore = $listButtonsSingle;
		} else {
			$createFirstTitle = ucfirst(sprintf($this->lang->l('createatomposition'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom'), $this->lang->l('createatomfirst')));
			$createLastTitle = ucfirst(sprintf($this->lang->l('createatomposition'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom'), $this->lang->l('createatomlast')));
			$listButtonsMore =
				'<a class="cmsButton" href="'.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>$owner->_id, PARAM_ID=>ID_NEW, PARAM_LIST=>$listInfo->_name, PARAM_ORDER=>1)).'" title="'.$createFirstTitle.'"><img src="elements/new_top.gif" alt="'.$createFirstTitle.'"></a>'
				.'<a class="cmsButton" href="'.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>$owner->_id, PARAM_ID=>ID_NEW, PARAM_LIST=>$listInfo->_name)).'" title="'.$createLastTitle.'"><img src="elements/new_bottom.gif" alt="'.$createLastTitle.'"></a>';
		}
		$parentInfo = $this->content->getParentInfo($owner);
		$grandParent = $this->content->getAtom($owner->_owner);
		$ownersOwningListInfo = $this->content->getListInfo($grandParent->_type, $owner->_list);
		if ($ownersOwningListInfo->_singleton) {
			$backTo = $grandParent;
		} else {
			$backTo = $owner;
		}
		if ($backTo->_id==$this->rootId) {
			$backButton = false;
		} else {
			$backButton = $this->page->button(
				BUTTON_BACK,
				ucfirst($this->lang->l('back')).' '.($backTo->_id==SITE_ID
					? $this->lang->l('tothemainmenu')
					: sprintf($this->lang->l('tothelistof'), $this->lang->l(LANG_PREFIX_LIST.$parentList->_name))
				),
				$this->_getParentUrl($backTo)
			);
		}
		print '<table class="commands" style="margin-bottom:0;" summary="">';
		if ($backButton) {
			print '<tr><td style="width:125px;">'.$backButton.'</td>';
		}
		print '<td>'.sprintf(
			$this->lang->l('thislistcontains'),
			(
				$owner->_id==SITE_ID
					? $this->lang->l(LANG_PREFIX_LIST.$listName)
					: sprintf(
						$this->lang->l('childof'),
						$this->lang->l(LANG_PREFIX_LIST.$listName),
						$this->_formatField($owner->_label)
					)
			)
		).'</td></tr></table>';
		print '<table class="commands" summary="">';
		if ($backButton) {
			print '<tr><td style="width:125px;"></td>';
		}
		print '<td>'.($listInfo->_fixed ? $this->lang->l('fixedlist') : ucfirst($createTitle).':&nbsp;</td><td>'.($this->content->getAtomCount($owner, $listInfo)==0 ? $listButtonsSingle : $listButtonsMore))
			."</td></tr></table>\n";

		// Determine potential list grouping possibilities
		$groups = array();
		foreach ($this->content->getAtomFields($listInfo->_target) as $field) {
			if (!in_array($field->_name, $listInfo->_fields)) {
				// Field not used as list column
				continue;
			}
			// Check the field type
			$groupCaptionPrefix = $this->lang->l(LANG_PREFIX_ATOM.$listInfo->_target.'.'.$field->_name).': ';
			switch ($field->_type) {
				case FIELD_TYPE_BOOLEAN:
					// Add 'yes' and 'no'
					$groups[] = array(
						'field' => $field->_name,
						'value' => 1,
						'caption' => $groupCaptionPrefix.$this->lang->l('valueyes'),
					);
					$groups[] = array(
						'field' => $field->_name,
						'value' => 0,
						'caption' => $groupCaptionPrefix.$this->lang->l('valueno'),
					);
					break;
				case FIELD_TYPE_ENUM:
					// Add all enum values
					foreach (explode(';',$field->_params[FIELD_PARAM_VALUES]) as $value) {
						$groups[] = array(
							'field' => $field->_name,
							'value' => $value,
							'caption' => $groupCaptionPrefix.$this->lang->l(LANG_PREFIX_ATOM.$listInfo->_target.'.'.$field->_name.'.'.$value),
						);
					}
					break;
				case FIELD_TYPE_LINK:
					// Add all linkable values
					$options = substr($field->_params[FIELD_PARAM_TARGET], 0, 1)=='#'
						? $this->_getTargetAtomsAll(substr($field->_params[FIELD_PARAM_TARGET], 1))
						: $this->_getTargetAtomsList($field->_params[FIELD_PARAM_TARGET]);
					asort($options);
					foreach ($options as $value=>$caption) {
						$groups[] = array(
							'field' => $field->_name,
							'value' => $value,
							'caption' => $groupCaptionPrefix.$caption,
						);
					}
					break;
			}
		}
		if (count($groups)>0) {
			// Add a first fake-group for showing all entries
			array_unshift(
				$groups,
				array(
					'caption' => $this->lang->l('filterallentries'),
				)
			);
			// Show grouping possibilites
			print '<form action="" method="GET" onsubmit="location.href=decodeURIComponent(filter.value);return false;">';
			print '<p>';
			print $this->lang->l('filtercaption').' ';
			print '<select name="filter">';
			foreach ($groups as $group) {
				print '<option value="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$ownerId, PARAM_LIST=>$listName, PARAM_FILTER=>$group['field'], PARAM_VALUE=>$group['value'])).'"';
				if ($group['field']==$showFilter
					&& $group['value']==$showValue) {
					print ' selected="selected"';
				}
				print '>'.ucfirst($group['caption'])."</option>\n";
			}
			print '</select> ';
			print $this->page->button(BUTTON_DISPLAY, $this->lang->l('filterbutton'));
			print '</p>';
			print "</form>\n";
		}

		// Create buttons for child lists of this atom type
		$childListButtons = array();
		$childLists = $this->content->getLists($listInfo->_target);
		foreach ($childLists as $targetList) {
			$maxDepth = intval($targetList->_maxdepth);
			if ($maxDepth>0) {
				// Check the current depth
				$depth = 0;
				$owningAtom = $owner;
				do {
					$owningAtom = $this->content->getAtom($owningAtom->_owner);
					$depth++;
				} while ($owningAtom);
				if ($depth>=$maxDepth) {
					// We have reached the maximum depth,
					// so do not display buttons for this sublist
					continue;
				}
			}
			if ($targetList->_singleton) {
				// Child list is a singleton list
				$childListButtons[] = '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>'%s', PARAM_ID=>'%s', PARAM_LIST=>$targetList->_name)).'" title="'.ucfirst(sprintf($this->lang->l('editsingleton'), $this->lang->l(LANG_PREFIX_LIST.$targetList->_name))).'">'.ucfirst($this->lang->l(LANG_PREFIX_LIST.$targetList->_name)).'</a>';
			} else {
				// Child list is a regular list
				$childListButtons[] = '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>'%s', PARAM_LIST=>$targetList->_name)).'" title="'.ucfirst(sprintf($this->lang->l('editlist'), $this->lang->l(LANG_PREFIX_LIST.$targetList->_name))).'">'.ucfirst($this->lang->l(LANG_PREFIX_LIST.$targetList->_name)).' (%d)</a>';
			}
		}

		// Determine the table columns from the field definition
		$cols = array();
		foreach ($listInfo->_fields as $field) {
			if ($field=='_label') {
				$cols[] = ucfirst($this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom'));
				$cols[] = 300;
			} else {
				$cols[] = ucfirst($this->lang->l(LANG_PREFIX_ATOM.$listInfo->_target.'.'.$field));
				$cols[] = 100;
			}
		}
		if (count($listInfo->_fields)==1) {
			// If there's only one field, use the atom type as column title
			$cols[0] = ucfirst($this->lang->l(LANG_PREFIX_LIST.$listName));
		}
		// Add columns for sub-list buttons
		for ($i=1; $i<=count($childListButtons); $i++) {
			$cols[] = '';
			$cols[] = 50;
		}
		// Add the command column
		$cols[] = '';
		$cols[] = 20;

		// Retrieve and display target objects
		$this->page->table_head($cols, 'list');
		$atoms = $this->content->getAtoms($owner->_id, $listInfo, $showIndex, $showAmount, $filterCondition);
		$i = 0;
		foreach ($atoms as $atom) {
			$i++;
			$editUrl = $this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_ID=>$atom->_id));
			$editTitle = ucfirst(sprintf($this->lang->l('editatom'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom')));
			$firstPrefix = '<img src="elements/blank.gif" width="'.($level*WIDTH_INDENT_LIST).'" height="20" alt="" />'
				.'<img src="elements/blank.gif" alt="" />'
				.'<img src="elements/blank.gif" width="10" height="20" alt="" />';
			if (!$listInfo->_fixed && $listInfo->_ordering==LIST_ORDERING_CUSTOM) {
				// No order defined, show buttons for manual ordering
				$moveupTitle = ucfirst(sprintf($this->lang->l('moveup'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom')));
				$movedownTitle = ucfirst(sprintf($this->lang->l('movedown'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom')));
				$firstPrefix .=
					($i<$totalAtomCount
						? '<a class="cmsButton" href="'.$this->page->link(array(PARAM_ACTION=>ACTION_DOWN, PARAM_ID=>$atom->_id)).'" title="'.$movedownTitle.'"><img src="elements/down.gif" alt="'.$movedownTitle.'" title="'.$movedownTitle.'"></a>'
						: '<a class="cmsButtonDisabled" title="'.$movedownTitle.' ('.$this->lang->l('notforlastelement').')"><img src="elements/down.gif" alt="'.$movedownTitle.'" title="'.$movedownTitle.' ('.$this->lang->l('notforlastelement').')"></a>'
					)
					.($i>1
						? '<a class="cmsButton" href="'.$this->page->link(array(PARAM_ACTION=>ACTION_UP, PARAM_ID=>$atom->_id)).'" title="'.$moveupTitle.'"><img src="elements/up.gif" alt="'.$moveupTitle.'" title="'.$moveupTitle.'"></a>'
						: '<a class="cmsButtonDisabled" title="'.$moveupTitle.' ('.$this->lang->l('notforfirstelement').')"><img src="elements/up.gif" alt="'.$moveupTitle.'" title="'.$moveupTitle.' ('.$this->lang->l('notforfirstelement').')"></a>'
					)
					.'<a name="'.ID_PREFIX_A.$atom->_id.'"><img src="elements/blank.gif" width="10" height="20" alt=""></a>';
			}
			$row = array();
			$first = true;
			foreach ($listInfo->_fields as $field) {
				$fieldValue = $this->_getFieldDisplayValue($field, $atom);
				if ($first) {
					$row[] = '<table summary=""><tr><td class="buttons">'.$firstPrefix.'</td><td>'.$fieldValue.'</td></tr></table>';
					$first = false;
				} else {
					$row[] = $fieldValue;
				}
			}

			// Add buttons for the child lists
			foreach ($childListButtons as $index=>$childListButton) {
				if ($childLists[$index]->_singleton) {
					// A singleton list: get child element first
					$child = @reset($this->content->getAtoms($atom->_id, $childLists[$index]));
					$row[] =  sprintf($childListButton, $atom->_id, $child ? $child->_id : ID_NEW);
				} else {
					// A regular list: get number of atoms in this list
					$cnt = $this->content->getAtomCount($atom, $childLists[$index]);
					$row[] =  sprintf($childListButton, $atom->_id, $cnt);
				}
			}

			// Add buttons for the custom actions
			$commands = '';
			if (is_array($this->actionsByList[strtolower($atom->_list)])) {
				foreach ($this->actionsByList[strtolower($atom->_list)] as $action) {
					$actionUrl = $this->page->link(array(PARAM_ACTION=>$action, PARAM_ID=>$atom->_id));
					$actionTitle = sprintf($this->lang->l(LANG_PREFIX_ACTION.$action), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name));
					$commands .= '<a class="cmsButton" href="'.$actionUrl.'" title="'.$actionTitle.'"><img src="'.$this->actionPaths[$action].$action.'.gif" alt="'.$actionTitle.'" title="'.$actionTitle.'"></a>';
				}
			}

			// Add preview button
			if (array_search($atom->_type, explode(',', $this->config->value(CONFIG_CMS_PREVIEW_TYPES)))!==false) {
				$previewUrl = sprintf($this->config->value(CONFIG_CMS_PREVIEW_URL, ''), $atom->_id);
				if ($previewUrl) {
					$previewTitle = ucfirst(sprintf($this->lang->l('previewatom'), $this->lang->l(LANG_PREFIX_ATOM.$atom->_type)));
					$commands .= '<a class="cmsButton" href="'.$previewUrl.'" title="'.$previewTitle.'" target="cms_preview"><img src="elements/preview.gif" alt="'.$previewTitle.'" title="'.$previewTitle.'"></a>';
				}
			}

			// Add buttons for the default actions
			$commands .= '<a class="cmsButton" href="'.$editUrl.'" title="'.$editTitle.'"><img src="elements/edit.gif" alt="'.$editTitle.'" title="'.$editTitle.'"></a>';
			if (!$listInfo->_fixed) {
				$deleteTitle = ucfirst(sprintf($this->lang->l('deleteatom'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom')));
				$commands .= '<a class="cmsButton" href="javascript:void(0)" onclick="deleteEntry(\''.$atom->_id.'\',\''.htmlentities($this->_formatField($atom->_label), 0, 'iso-8859-1').'\');" title="'.$deleteTitle.'"><img src="elements/delete.gif" alt="'.$deleteTitle.'" title="'.$deleteTitle.'"></a>';
			}
			$row[] = $commands;
			// Print the row
			$this->page->table_row($row);
		}
		$numAtoms = $i;
		$this->page->table_foot();

		// Print list summary
		if ($numAtoms==0) {
			$numText = 'entriestotalnone';
		} else if ($numAtoms==1) {
			$numText = 'entriestotalone';
		} else {
			$numText = 'entriestotalmore';
		}
		print '<p class="listSummary" style="float:left;">'.sprintf($this->lang->l($numText), $showIndex+1, $showIndex+$numAtoms, $totalAtomCount)."</p>\n";

		// Show the pagination widget
		if ($totalAtomCount>=$showAmount) {
			print '<p class="listSummary" style="float:right;">';
			print $showIndex-$showAmount>=0
				? '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$ownerId, PARAM_LIST=>$listName, PARAM_INDEX=>$showIndex-$showAmount, PARAM_AMOUNT=>$showAmount, PARAM_FILTER=>$showFilter, PARAM_VALUE=>$showValue)).'">'.$this->lang->l('pageprevious').'</a>'
				: $this->lang->l('pageprevious');
			print '&nbsp;&nbsp;|&nbsp;&nbsp;';
			print $showIndex+$showAmount<$totalAtomCount
				? '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$ownerId, PARAM_LIST=>$listName, PARAM_INDEX=>$showIndex+$showAmount, PARAM_AMOUNT=>$showAmount, PARAM_FILTER=>$showFilter, PARAM_VALUE=>$showValue)).'">'.$this->lang->l('pagenext').'</a>'
				: $this->lang->l('pagenext');
			print '</p>';
		}

		print '</div>';
		print '</div>';
		$this->page->close();
	}


	/**
	 * Show an atom edit mask. The atom which is going to be edited
	 * is specified by POST data.
	 */
	function showEditMaskPOST() {
		// Edit an atom
		$id = $this->_getParam(PARAM_ID);
		$order = $this->_getParam(PARAM_ORDER, true);
		if (!$order) {
			$order = '';
		}

		// Get the atom to edit
		if ($id==ID_NEW) {
			// This is a new object, so create a new atom
			$owner = $this->content->getAtom($this->_getParam(PARAM_OWNER));
			if (!$owner) {
				echo $this->lang->l('error.nosuchatom');
				exit;
			}
			$listInfo = $this->content->getListInfo($owner->_type, $this->_getParam(PARAM_LIST));
			if (!$listInfo) {
				$listInfo = $this->content->getListInfo(SITE, $this->_getParam(PARAM_LIST));
			}
			if (!$listInfo) {
				echo $this->lang->l('error.nosuchlist');
				exit;
			}
			$atom = $this->content->newAtom($listInfo->_target, $owner, $listInfo->_name, false);
			$head = $this->lang->l('createatom');
		} else {
			// Get the object's data
			$atom = $this->content->getAtom($id);
			if (!$atom) {
				echo $this->lang->l('error.nosuchatom');
				exit;
			}
			$owner = $this->content->getAtom($atom->_owner);
			$listInfo = $this->content->getListInfo($owner->_type, $atom->_list);
			if (!$listInfo) {
				$listInfo = $this->content->getListInfo(SITE, $atom->_list);
			}
			if (!$listInfo) {
				echo $this->lang->l('error.nosuchlist');
				exit;
			}
			$head = $this->lang->l('editatom');
		}
		if ($listInfo->_singleton) {
			$title = $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name);
			$formTitle = $title;
		} else if ($owner->_id!=ID_NULL) {
			$title = sprintf($this->lang->l('childof'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name), $this->_formatField($owner->_label));
			$formTitle = $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom');
		} else {
			$title = $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name.'.atom');
			$formTitle = $title;
		}
		$head = sprintf(ucfirst($head), $title);

		// Open the page
		$this->page->open($head, $head);

		print "<div id=\"container\">\n";

		if (!$this->rootId) {
			$this->_printNavigation();
		}
		print '<div id="page"';
		if ($this->_getParam(PARAM_CALLING_FIELD, true)!='' || $this->rootId) {
			echo ' class="popup"';
		}
		print '>';

		// Check the access rights
		if (!$this->content->canAccess($atom, '-', $this->getUsername())) {
			// Access not allowed
			echo '<p>'.$this->lang->l('error.accessnotallowed')."</p>\n";
			print '</div>';
			print '</div>';
			$this->page->close();
			return true;
		}

?><script language="JavaScript" type="text/javascript">
function _addItem(target, text, value) {
	j = target.options.length;
	while (j>0 && target.options[j-1].text>text) {
		target.options[j] = new Option(target.options[j-1].text, target.options[j-1].value);
		j--;
	}
	target.options[j] = new Option(text, value);
}
function _saveSelectedItems(field) {
	source = document.content.elements[field+"Selection"];
	selected = "";
	for (i=0; i<source.options.length; i++) {
		selected += "<?php print ID_SEPARATOR; ?>"+source.options[i].value;
	}
	document.content.elements[field].value = selected.substr(1);
}
function moveItem(field, sourceName, targetName) {
	source = document.content.elements[sourceName];
	target = document.content.elements[targetName];
	i = 0;
	copied = 0;
	while (i<source.options.length) {
		if (source.options[i].selected) {
			_addItem(target, source.options[i].text, source.options[i].value);
			source.options[i] = null;
			copied++;
		} else {
			i++;
		}
	}
	if (copied==0) {
		alert('<?php print $this->lang->l('pleaseselectanatom'); ?>');
	} else {
		_saveSelectedItems(field);
	}
}
function addItem(field, text, value) {
	if (document.content.elements[field+"Selection"]) {
		// Multiselect
		_addItem(document.content.elements[field+"Selection"], text, value);
		_saveSelectedItems(field);
	} else {
		// Single select combobox
		_addItem(document.content.elements[field], text, value);
		document.content.elements[field].value = value;
	}
}
function showVariant(variant) {
	for (i=0; i<visibleElements.length; i++) {
		visibleElements[i].style.display = "none";
	}
	visibleElements = new Array();
	for (i=0; i<document.content.elements.length; i++) {
		if (document.content.elements[i].name.indexOf(variant+'_')==0) {
			el = document.content.elements[i].parentNode.parentNode;
			el.style.display = "table-row";
			visibleElements[visibleElements.length] = el;
		}
	}
}
</script>
<?php

		// Print the edit form
		$this->page->form_head('return checkForm()');
		$this->page->hidden(PARAM_ID, $id);
		$this->page->hidden(PARAM_ACTION, ACTION_SAVE);
		$this->page->hidden(PARAM_OWNER, $owner->_id);
		$this->page->hidden(PARAM_LIST, $listInfo->_name);
		$this->page->hidden(PARAM_ORDER, $order);
		$this->page->hidden(PARAM_CALLING_FIELD, $this->_getParam(PARAM_CALLING_FIELD, true));
		$this->page->table_head(array(ucfirst($formTitle), 250, '', 600), 'edit');
		$firstFieldName = '';
		$checkJS = '';
		$containsImages = false;
		$visibleElements = array();
		if (count($this->content->getLanguages())==1) {
			$langName = '';
		} else {
			$langName = ' (%s)';
		}
		$atomInfo = $this->content->getAtomInfo($atom->_type);
		foreach ($this->content->getAtomFields($atom->_type) as $index=>$field) {
			if (isset($atomInfo['section'][$index-1])) {
				$this->page->table_row(array('<b>'.ucfirst($this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$atomInfo['section'][$index-1]['name'])).'</b><hr />', ''));
			}
			$fieldNames = array();
			if ($field->_params[FIELD_PARAM_READONLY]==FIELD_VALUE_YES) {
				// Readonly field - display it like it would be displayed in a list
				$input = $this->_getFieldDisplayValue($field->_name, $atom);
			} else {
				// Get input widgets
				switch ($field->_type) {
					case FIELD_TYPE_STRING:
						$input = array();
						foreach ($this->content->getLanguages() as $lang) {
							$fieldNameLang = $this->_extendByLanguage($field->_name, $lang);
							$inputString = $this->page->input_text(
									$fieldNameLang,
									htmlentities($atom->{$fieldNameLang} ? $atom->{$fieldNameLang} : $field->_params[FIELD_PARAM_DEFAULT], 0, 'iso-8859-1'),
									$field->_params[FIELD_PARAM_SIZE]
								);
							if ($field->_params[FIELD_PARAM_FORMAT]) {
								$formatString = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.LANG_SUFFIX_FORMAT);
								$inputString .= ' ('.$this->lang->l('format').': '.$formatString.')';
								$checkJS .= 'if (!document.content.'.$fieldNameLang.'.value.match(/'.$field->_params[FIELD_PARAM_FORMAT].'/)) {'
									.'alert("'.sprintf($this->lang->l('formatdisplay'), $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name), $formatString).'");'
									.'document.content.'.$fieldNameLang.'.focus();'
									.'document.content.'.$fieldNameLang.'.select();'
									.'return false;'
									.'}';
							}
							$label[$fieldNameLang] = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name).sprintf($langName, $this->lang->l(LANG_PREFIX_LANGUAGE.$lang));
							$input[$fieldNameLang] = $inputString;
							$fieldNames[] = $fieldNameLang;
						}
						break;
					case FIELD_TYPE_TEXT:
					case FIELD_TYPE_PLAINTEXT:
						$input = array();
						foreach ($this->content->getLanguages() as $lang) {
							$fieldNameLang = $this->_extendByLanguage($field->_name, $lang);
							$label[$fieldNameLang] = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name).sprintf($langName, $this->lang->l(LANG_PREFIX_LANGUAGE.$lang));
							$input[$fieldNameLang] =
								$this->page->input_memo(
									$fieldNameLang,
									htmlentities($atom->{$fieldNameLang} ? $atom->{$fieldNameLang} : $field->_params[FIELD_PARAM_DEFAULT], 0, 'iso-8859-1'),
									$field->_params[FIELD_PARAM_SIZE] ? $field->_params[FIELD_PARAM_SIZE] : 10,
									$field->_type==FIELD_TYPE_PLAINTEXT ? 'plaintext' : 'text'
								);
							$fieldNames[] = $fieldNameLang;
						}
						break;
					case FIELD_TYPE_CHARACTER:
						if ($field->_params[FIELD_PARAM_LINES]>1) {
							// Multiline input field
							$input = $this->page->input_memo(
									$field->_name,
									htmlentities($atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT], 0, 'iso-8859-1'),
									$field->_params[FIELD_PARAM_LINES],
									'plaintext'
								);
						} else {
							// Single line input field
							$input = $this->page->input_text(
									$field->_name,
									htmlentities($atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT], 0, 'iso-8859-1'),
									$field->_params[FIELD_PARAM_SIZE]
								);
						}
						if ($field->_params[FIELD_PARAM_FORMAT]) {
							$formatString = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.LANG_SUFFIX_FORMAT);
							$input .= ' ('.$this->lang->l('format').': '.$formatString.')';
							$checkJS .= 'if (!document.content.'.$field->_name.'.value.match(/'.$field->_params[FIELD_PARAM_FORMAT].'/)) {'
								.'alert("'.sprintf($this->lang->l('formatdisplay'), $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name), $formatString).'");'
								.'document.content.'.$field->_name.'.focus();'
								.'document.content.'.$field->_name.'.select();'
								.'return false;'
								.'}';
						}
						break;
					case FIELD_TYPE_DATE:
						// Single line input field
						$date = explode('-', $atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT]);
						if (count($date)<3) {
							$date = explode('-', date('Y-m-d'));
						}
						$dayValues = array(0 => '--');
						for ($i=1; $i<=31; $i++) {
							$dayValues[$i] = sprintf('%02d', $i);
						}
						$monthValues = array(0 => '--');
						for ($i=1; $i<=12; $i++) {
							$monthValues[$i] = sprintf('%02d', $i);
						}
						$yearValues = array(0 => '--');
						for ($i=$field->_params[FIELD_PARAM_FIRST] ? $field->_params[FIELD_PARAM_FIRST] : 2000; $i<=($field->_params[FIELD_PARAM_LAST] ? $field->_params[FIELD_PARAM_LAST] : 2020); $i++) {
							$yearValues[$i] = $i;
						}
						$dateFormat = $this->lang->l('dateformat');
						$input = '';
						for ($i=0; $i<strlen($dateFormat); $i++) {
							switch ($dateFormat[$i]) {
								case 'D':
									$input .= '&nbsp;'.$this->page->input_select(
										$field->_name.'_day',
										$dayValues,
										$date[2],
										1,
										false,
										"if (this.value==0) { document.content.".$field->_name."_year.value=0; document.content.".$field->_name."_month.value=0; }"
									);
									break;
								case 'M':
									$input .= '&nbsp;'.$this->page->input_select(
										$field->_name.'_month',
										$monthValues,
										$date[1],
										1,
										false,
										"if (this.value==0) { document.content.".$field->_name."_year.value=0; document.content.".$field->_name."_day.value=0; }"
									);
									break;
								case 'Y':
									$input .= '&nbsp;'.$this->page->input_select(
										$field->_name.'_year',
										$yearValues,
										$date[0],
										1,
										false,
										"if (this.value==0) { document.content.".$field->_name."_day.value=0; document.content.".$field->_name."_month.value=0; }"
									);
							}
						}
						$input = '<span class="date">'.substr($input, 6).'</span>';
						break;
					case FIELD_TYPE_COLOR:
						$color = $atom->{$field->_name}
							? $atom->{$field->_name}
							: ($field->_params[FIELD_PARAM_DEFAULT]
								? $field->_params[FIELD_PARAM_DEFAULT]
								: 'ffffff');
						$input = '<input selectedcolor="#'.$color.'" type="text" value="'.$color.'" class="narrow" colorbox="true" id="'.$field->_name.'" name="'.$field->_name.'" />
	<script type="text/javascript">
		var myCP = dhtmlXColorPickerInput("'.$field->_name.'");
		myCP.setImagePath("dhtmlxColorPicker/imgs/");
		myCP.init();
	</script>';
						break;
					case FIELD_TYPE_IMAGE:
						$input = $this->page->input_picture(
							$field->_name,
							$atom->{$field->_name}=='' ? false : PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name}
						);
						$containsImages = true;
						break;
					case FIELD_TYPE_FILE:
						$input = $this->page->input_file(
							$field->_name,
							$atom->{$field->_name}=='' ? false : PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].rawurlencode($atom->{$field->_name})
						);
						break;
					case FIELD_TYPE_BOOLEAN:
						$input = $this->page->input_checkbox(
							$field->_name,
							$this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.'.checked'),
							$atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT]
						);
						break;
					case FIELD_TYPE_INTEGER:
						$input = $this->page->input_text(
								$field->_name,
								$atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT],
								10
							);
						break;
					case FIELD_TYPE_ENUM:
						// Parse values
						$values = explode(';',$field->_params[FIELD_PARAM_VALUES]);
						$options = array();
						foreach ($values as $value) {
							$options[$this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.'.'.$value)] = $value;
						}
						// Create selection box
						$input = $this->page->input_radio(
							$field->_name,
							$options,
							$atom->{$field->_name} ? $atom->{$field->_name} : $field->_params[FIELD_PARAM_DEFAULT]
						);
						break;
					case FIELD_TYPE_VARIANT:
						// Parse values
						$values = explode(';',$field->_params[FIELD_PARAM_VALUES]);
						$options = array();
						foreach ($values as $value) {
							$options[$value] = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.'.'.$value);
						}
						// Create selection box
						$input = $this->page->input_select(
							$field->_name,
							$options,
							$atom->{$field->_name},
							1,
							false,
							"showVariant(this.value);"
						);
						$variant = $atom->{$field->_name}
							? $atom->{$field->_name}: @reset(array_keys($options));
						break;
					case FIELD_TYPE_LINK:
						// Collect target items
						$options = substr($field->_params[FIELD_PARAM_TARGET], 0, 1)=='#'
							? $this->_getTargetAtomsAll(substr($field->_params[FIELD_PARAM_TARGET], 1))
							: $this->_getTargetAtomsList($field->_params[FIELD_PARAM_TARGET]);
						asort($options);
						// Create selection combobox
						$options = array(ID_NULL=>$this->lang->l('removeatomlink'))+$options;
						$input = $this->page->input_select(
							$field->_name,
							$options,
							$atom->{$field->_name},
							1,
							false
						);
						if (substr($field->_params[FIELD_PARAM_TARGET], 0, 1)!='#') {
							// Add a button to create items if we don't refer
							// to an atom type
							$createTitle = ucfirst(sprintf($this->lang->l('createatom'), $this->lang->l(LANG_PREFIX_LIST.$field->_params[FIELD_PARAM_TARGET].'.atom')));
							$createListInfo = $this->content->getListInfo(SITE, $field->_params[FIELD_PARAM_TARGET]);
							if ($this->content->canAccess($this->content->getAtom(SITE_ID), $createListInfo->_name, $this->getUsername())) {
								// The user has write access to this list
								$input = '<table class="select"><tr><td>'.$input.'</td><td>&nbsp;</td><td><a class="cmsButton" href="javascript:win=window.open(\''.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>SITE_ID, PARAM_ID=>ID_NEW, PARAM_LIST=>$createListInfo->_name, PARAM_ORDER=>1, PARAM_CALLING_FIELD=>$field->_name)).'\',\''.uniqid('cmsedit_').'\',\'width=750,height=600,directories=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,titlebar=1,toolbar=0\').focus();" title="'.$createTitle.'"><img src="elements/new.gif" alt="'.$createTitle.'"></a></td></tr></table>';
							}
						}
						break;
					case FIELD_TYPE_MULTILINK:
						if (substr($field->_params[FIELD_PARAM_TARGET], 0, 1)=='#') {
							// We refer to an atom type, not to a list
							// We cannot add new items in this case
							$createButton = '';
							// Collect target items
							$options = $this->_getTargetAtomsAll(substr($field->_params[FIELD_PARAM_TARGET], 1));
						} else {
							// Add a button to create items
							$createTitle = ucfirst(sprintf($this->lang->l('createatom'), $this->lang->l(LANG_PREFIX_LIST.$field->_params[FIELD_PARAM_TARGET].'.atom')));
							$createListInfo = $this->content->getListInfo(SITE, $field->_params[FIELD_PARAM_TARGET]);
							if ($this->content->canAccess($this->content->getAtom(SITE_ID), $createListInfo->_name, $this->getUsername())) {
								// The user has write access to this list
								$createButton = '<a class="cmsButton" href="javascript:win=window.open(\''.$this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>SITE_ID, PARAM_ID=>ID_NEW, PARAM_LIST=>$createListInfo->_name, PARAM_ORDER=>1, PARAM_CALLING_FIELD=>$field->_name)).'\',\''.uniqid('cmsedit_').'\',\'width=750,height=600,directories=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,titlebar=1,toolbar=0\').focus();" title="'.$createTitle.'"><img src="elements/new.gif" alt="'.$createTitle.'"></a><br>';
							} else {
								$createButton = '';
							}
							// Collect target items
							$options = $this->_getTargetAtomsList($field->_params[FIELD_PARAM_TARGET]);
						}
						// Create two multi-selection listboxes
						$selectedOptions = array();
						foreach (explode(ID_SEPARATOR,$atom->{$field->_name}) as $selectedAtomId) {
							$selectedAtom = $this->content->getAtom($selectedAtomId);
							if ($selectedAtom) {
								$selectedOptions[$selectedAtom->_id] = $this->_formatField($selectedAtom->_label);
							}
						}
						asort($selectedOptions);
						$options = array_diff($options, $selectedOptions);
						$input = '<input type="hidden" name="'.$field->_name.'" value="'.implode(ID_SEPARATOR, array_keys($selectedOptions)).'">'
							.'<table class="select">'
							.'<tr><td>'.$this->lang->l('selected').':</td><td class="selectMiddle" rowspan="2">'
							.$createButton
							.'<a class="cmsButton" href="javascript:moveItem(\''.$field->_name.'\',\''.$field->_name.'Options\',\''.$field->_name.'Selection\');" title="'.$this->lang->l('addlinkedatom').'"><img src="elements/left.gif"></a>'
							.'<br>'
							.'<a class="cmsButton" href="javascript:moveItem(\''.$field->_name.'\',\''.$field->_name.'Selection\',\''.$field->_name.'Options\');" title="'.$this->lang->l('removelinkedatom').'"><img src="elements/right.gif"></a>'
							.'</td><td>'.$this->lang->l('available').':</td></tr>'
							.'<tr><td>'
							.$this->page->input_select(
								$field->_name.'Selection',
								$selectedOptions,
								'',
								$field->_params[FIELD_PARAM_SIZE] ? $field->_params[FIELD_PARAM_SIZE] : 5,
								true
							)
							.'</td><td>'
							.$this->page->input_select(
								$field->_name.'Options',
								$options,
								'',
								$field->_params[FIELD_PARAM_SIZE] ? $field->_params[FIELD_PARAM_SIZE] : 5,
								true
							).'</td></tr></table>';
						break;
					default:
						$input = sprintf($this->lang->l('error.typeunknown'), $field->_type);
				}
			}
			if (count($fieldNames)==0) {
				$fieldNames = array($field->_name);
				$label = array($field->_name => $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name));
				$input = array($field->_name => $input);
			}
			$i = strpos($field->_name, '_');
			if ($i===false) {
				$visible = true;
			} else {
				$visible = strpos($field->_name, $variant)!==false;
				if ($visible) {
					$visibleElements[] = 'document.content.'.$field->_name.'.parentNode.parentNode';
				}
			}
			$fieldComment = $this->lang->l(LANG_PREFIX_ATOM.$atom->_type.'.'.$field->_name.'.comment', '');
			if ($fieldComment!='') {
				$fieldComment = '<br />'.$fieldComment;
			}
			foreach ($fieldNames as $fieldName) {
				$this->page->table_row(
					array(ucfirst($label[$fieldName]).':', $input[$fieldName].$fieldComment),
					'',
					false,
					$visible ? '' : 'style="display:none;"'
				);
			}
		}
		$this->page->table_foot();
		if ($containsImages && !$_COOKIE[SESSION_HIDE_UPLOAD_MSG]) {
			$this->page->upload_msg();
		}
		if ($this->_getParam(PARAM_CALLING_FIELD, true)
			|| $this->rootId==$atom->_id) {
			$cancelUrl = 'javascript:close()';
		} else if ($listInfo->_singleton) {
			if ($atom->_owner==SITE_ID) {
				$cancelUrl = $this->page->link();
			} else {
				$cancelUrl = $this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$owner->_owner, PARAM_LIST=>$owner->_list));
			}
		} else {
			$cancelUrl = $this->_getParentUrl($atom);
		}
		$this->page->form_foot($cancelUrl);
?><script language="javascript" type="text/javascript">
function checkForm() {
	<?php print $checkJS; ?>
	return true;
}
var visibleElements = new Array(<?php print implode(', ', $visibleElements); ?>);
</script>
<?php
		print '</div>';
		print '</div>';
		$this->page->close();
	}


	function _resizeImage($filename, $width, $height) {
		// Open file
		list($srcWidth, $srcHeight, $type) = getimagesize($filename);
		switch ($type) {
			case 1:
				$srcImage = imagecreatefromgif($filename);
				$truecolor = false;
				break;
			case 2:
				$srcImage = imagecreatefromjpeg($filename);
				$truecolor = true;
				break;
			case 3:
				$srcImage = imagecreatefrompng($filename);
				$truecolor = true;
				break;
			default:
				// We cannot deal with this image format
				return false;
		}
		if (!$srcImage) {
			return false;
		}

		// Determine image size
		$srcWidth = ImageSX($srcImage);
		$srcHeight = ImageSY($srcImage);
		$ratioHeight = $height>0 ? $srcHeight/$height : 1;
		$ratioWidth = $width>0 ? $srcWidth/$width : 1;
		if ($ratioWidth>1 || $ratioHeight>1) {
			// Image is wider or higher than the destination width and height
			// Resize to fit the destination dimensions
			if ($ratioWidth>$ratioHeight) {
				$destWidth = $width;
				$destHeight = floor($srcHeight/$ratioWidth);
			} else {
				$destWidth = floor($srcWidth/$ratioHeight);
				$destHeight = $height;
			}
			// Create the destination image with the new width and height
			$destImage = $truecolor
				? ImageCreateTrueColor($destWidth, $destHeight)
				: imagecreate($destWidth, $destHeight);
			if (!$destImage) {
				return false;
			}
			switch ($type) {
				case 1:
					// GIF
					$trnprt_indx = imagecolortransparent($srcImage);
					if ($trnprt_indx >= 0) {
						//its transparent
						$trnprt_color = imagecolorsforindex($srcImage, $trnprt_indx);
						$trnprt_indx = imagecolorallocate($destImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
						imagefill($destImage, 0, 0, $trnprt_indx);
						imagecolortransparent($destImage, $trnprt_indx);
					}
					break;
				case 3:
					// PNG
					imagealphablending($destImage, false);
					$colorTransparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
					imagefill($destImage, 0, 0, $colorTransparent);
					imagesavealpha($destImage, true);
					break;
				default:
					Imagefill($destImage, 0, 0, imagecolorallocate($destImage, 255, 255, 255));
			}
			if (!ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth+1, $destHeight+1, $srcWidth, $srcHeight)) {
				return false;
			}
			// Save the image
			switch ($type) {
				case 1:
					$saved = imagegif($destImage, $filename);
					break;
				case 2:
					$saved = imagejpeg($destImage, $filename, 80);
					break;
				case 3:
					$saved = imagepng($destImage, $filename);
					break;
			}
			if (!$saved) {
				return false;
			}
			ImageDestroy($destImage);
		}

		// Free the memory used for the src image
		ImageDestroy($srcImage);

		return true;
	}


	/**
	 * Saves an atom. The atom and its contents are specified by POST data.
	 */
	function saveAtomPOST() {
		// Check wether the required parameters are given
		$id = $this->_getParam(PARAM_ID);
		$owner = $this->content->getAtom($this->_getParam(PARAM_OWNER));
		$listInfo = $this->content->getListInfo($owner->_type, $this->_getParam(PARAM_LIST));
		if (!$listInfo) {
			$listInfo = $this->content->getListInfo(SITE, $this->_getParam(PARAM_LIST));
		}

		// Create the atom and fill it with values from POST data
		if ($id==ID_NEW) {
			$atom = $this->content->newAtom($listInfo->_target, $owner, $listInfo->_name);
			$id = $atom->_id;
			// Set the atom's desired position from POST data
			$atom->_order = $this->_getParam(PARAM_ORDER, true);
			$action = CMS_ACTION_NEW;
		} else {
			$atom = $this->content->getAtom($id);
			$action = CMS_ACTION_EDIT;
		}

		foreach ($this->content->getAtomFields($listInfo->_target) as $field) {
			if ($field->_params[FIELD_PARAM_READONLY]==FIELD_VALUE_YES) {
				// Readonly field - don't allow for a value change
				continue;
			}
			switch ($field->_type) {
				case FIELD_TYPE_IMAGE:
					// Image fields need special handling
					if ($this->_getParam('del'.$field->_name, true)) {
						// Deletion of the image was requested
						@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
						@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_THUMB_PREFIX.$atom->{$field->_name});
						@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_PREVIEW_PREFIX.$atom->{$field->_name});
						$atom->{$field->_name} = '';
					} else {
						// Check wether an image was uploaded
						$file = $_FILES[$field->_name];
				  		if (($file['error']==0) && ($file['size']>0)) {
				  			$size = getimagesize($file['tmp_name']);
				  			if ($size[2]<=3) {
				  				// The image is GIF, JPG, or PNG
					    		$basename = sprintf("%06d", $id).strtolower($field->_name).$GLOBALS['PATH_IMAGE_SUFFIX'][$size[2]];
					    		$fn = PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$basename;
					  			$fnThumb = PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_THUMB_PREFIX.$basename;
					  			$fnPreview = PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_PREVIEW_PREFIX.$basename;
					  			// Delete the old images (possibly with another suffix)
								@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
								@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_THUMB_PREFIX.$atom->{$field->_name});
								@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_PREVIEW_PREFIX.$atom->{$field->_name});
					  			// Move image to its definite location and resize it
					  			move_uploaded_file($file['tmp_name'], $fn);
					  			chmod($fn, 0644);
				  				$this->_resizeImage(
				  					$fn,
				  					$field->_params[FIELD_PARAM_WIDTH],
				  					$field->_params[FIELD_PARAM_HEIGHT]
				  				);
					  			// Create thumbnail image
					  			if ($field->_params[FIELD_PARAM_THUMB_WIDTH] || $field->_params[FIELD_PARAM_THUMB_HEIGHT]) {
						  			copy($fn, $fnThumb);
						  			chmod($fnThumb, 0644);
					  				$this->_resizeImage(
					  					$fnThumb,
					  					$field->_params[FIELD_PARAM_THUMB_WIDTH],
					  					$field->_params[FIELD_PARAM_THUMB_HEIGHT]
					  				);
					  			}
					  			// Create preview image
					  			if ($field->_params[FIELD_PARAM_PREVIEW_WIDTH] || $field->_params[FIELD_PARAM_PREVIEW_HEIGHT]) {
						  			copy($fn, $fnPreview);
						  			chmod($fnPreview, 0644);
					  				$this->_resizeImage(
					  					$fnPreview,
					  					$field->_params[FIELD_PARAM_PREVIEW_WIDTH],
					  					$field->_params[FIELD_PARAM_PREVIEW_HEIGHT]
					  				);
					  			}
				  				// Store the image's basename
				  				$atom->{$field->_name} = $basename;
				  			}
				  		}
					}
					$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $field->_name, $field->_type);
					break;
				case FIELD_TYPE_FILE:
					// File fields need special handling
					if ($this->_getParam('del'.$field->_name, true)) {
						// Deletion of the file was requested
						@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
						$atom->{$field->_name} = '';
					} else {
						// Check wether a file was uploaded
						$file = $_FILES[$field->_name];
				  		if (($file['error']==0) && ($file['size']>0)) {
				  			// Delete existing file
							@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
							// Create new filename
				  			$i = 0;
				  			do {
				  				$pos = strrpos($file['name'], '.');
								if ($pos===false || $pos==0) {
									$ext = '';
									$name = $file['name'];
								} else {
									$ext = substr($file['name'], $pos);
									$name = substr($file['name'], 0, $pos);
								}
				    			$basename = $name.($i>0 ? '_'.sprintf('%02d', $i) : '').$ext;
				    			$fn = PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$basename;
				    			$i++;
				  			} while (file_exists($fn));
				  			// Copy file
				  			move_uploaded_file($file['tmp_name'], $fn);
				  			chmod($fn, 0644);
			  				// Store the file's basename
			  				$atom->{$field->_name} = $basename;
				  		}
					}
					$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $field->_name, $field->_type);
					break;
				case FIELD_TYPE_STRING:
				case FIELD_TYPE_TEXT:
				case FIELD_TYPE_PLAINTEXT:
					// Multi-language field types
					foreach ($this->content->getLanguages() as $lang) {
						$fieldNameLang = $this->_extendByLanguage($field->_name, $lang);
						$atom->{$fieldNameLang} = $this->_getParam($fieldNameLang);
						$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $fieldNameLang, $field->_type);
					}
					break;
				case FIELD_TYPE_BOOLEAN:
					$atom->{$field->_name} = $this->_getParam($field->_name, true);
					$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $field->_name, $field->_type);
					break;
				case FIELD_TYPE_DATE:
					$atom->{$field->_name} = sprintf(
						'%04d-%02d-%02d',
						$this->_getParam($field->_name.'_year', true),
						$this->_getParam($field->_name.'_month', true),
						$this->_getParam($field->_name.'_day', true)
					);
					$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $field->_name, $field->_type);
					break;
				default:
					// All other field types are treated as simple character data
					$atom->{$field->_name} = $this->_getParam($field->_name);
					$this->_fireFieldEvent($atom, EVENT_TYPE_SAVED, $field->_name, $field->_type);
			}
		}

		// Fire a "before save" event
		$this->_fireAtomEvent($atom, EVENT_TYPE_BEFORESAVE);

		// Save the atom
		$this->content->putAtom($atom);

		// Fire a "saved" event
		$this->_fireAtomEvent($atom, EVENT_TYPE_SAVED);

		// Expand the parent atom
		$_SESSION[SESSION_EXPANDED][] = $atom->_owner;

		// Store wether to show upload msgs
		if ($this->_getParam(PARAM_HIDE_UPLOAD_MSG, true)) {
			setcookie(SESSION_HIDE_UPLOAD_MSG, 'true', time()*60*60*24*365);
		}

		if ($this->_getParam(PARAM_CALLING_FIELD, true)!='') {
			// Close the window if called by a link/multilink field
?><script language="javascript" type="text/javascript">
opener.addItem("<?php print $this->_getParam(PARAM_CALLING_FIELD, true); ?>", "<?php print $atom->_label; ?>", "<?php print $atom->_id; ?>");
close();
</script>
<?php
		} else if (($this->rootId==$atom->_id) || ($action==CMS_ACTION_NEW && $this->rootId==ID_NEW)) {
			// Close the window if we are only allowed to edit this item
?><script language="javascript" type="text/javascript">
if (window.opener) {
	window.opener.location.reload();
}
close();
</script>
<?php
		} else if ($listInfo->_singleton) {
			if ($atom->_owner==SITE_ID) {
				$this->page->redirect_url($this->page->link());
			} else {
				$this->page->redirect_url($this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$owner->_owner, PARAM_LIST=>$owner->_list)));
			}
		} else {
			// Return to the parent list
			$this->page->redirect_url($this->_getParentUrl($atom).'#'.ID_PREFIX_A.$atom->_id);
		}
		exit;
	}


	function _fireAtomEvent(&$atom, $type) {
		foreach (array($atom->_type, 'Atom') as $name) {
			$func = 'cms'.ucfirst($name).ucfirst($type);
			if (function_exists($func)) {
				$func($atom);
			}
		}
	}


	function _fireFieldEvent(&$atom, $type, $fieldName, $fieldType) {
		foreach (array($atom->_type, 'Atom') as $name) {
			$func = 'cms'.ucfirst($name).ucfirst($type).'_field';
			if (function_exists($func)) {
				$func($atom, $fieldName, $fieldType);
			}
		}
	}


	/**
	 * Delete atom.
	 */
	function deleteAtomPOST() {
		$atom = $this->content->getAtom($this->_getParam(PARAM_ID));

		// Fire a "delete" event
		$this->_fireAtomEvent($atom, EVENT_TYPE_DELETE);

		// Delete all images
		foreach ($this->content->getAtomFields($atom->_type) as $field) {
			if ($field->_type==FIELD_TYPE_IMAGE) {
				@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
				@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_THUMB_PREFIX.$atom->{$field->_name});
				@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].PATH_PREVIEW_PREFIX.$atom->{$field->_name});
			} else if ($field->_type==FIELD_TYPE_FILE) {
				@unlink(PATH_PREFIX.$field->_params[FIELD_PARAM_PATH].$atom->{$field->_name});
			}
		}

		$this->content->removeAtom($atom);
		$this->page->redirect_url($this->_getParentUrl($atom).'#'.ID_PREFIX_A.$atom->_owner);
		exit;
	}


	/**
	 * Get the URL of the parent list view for this atom. This is the smallest list view the atom
	 * is visible in.
	 *
	 * @param atom $atom
	 * @return the URL of the parent list view for the specified atom.
	 */
	function _getParentUrl($atom) {
		$parent = $this->content->getParentInfo($atom);
		if ($parent) {
			$params = array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$parent['atom'], PARAM_LIST=>$parent['list']);
		} else {
			$params = array();
		}
		return $this->page->link($params);
	}


	function moveAtomPOST($direction) {
		$atom = $this->content->getAtom($this->_getParam(PARAM_ID));

		// Move article one position down
		$this->content->moveAtom($atom, $direction);
		// Back to list
		$this->page->redirect_url($this->_getParentUrl($atom).'#'.ID_PREFIX_A.$atom->_id);
		exit;
	}


	function expandPOST() {
		$id = $this->_getParam(PARAM_ID);
		$owner = $this->content->getAtom($this->_getParam(PARAM_OWNER));
		$listInfo = $this->content->getListInfo($owner->_type, $this->_getParam(PARAM_LIST));
		if (in_array($id, $_SESSION[SESSION_EXPANDED])) {
			// To collapse, delete id from list of expanded ids
			array_splice(
				$_SESSION[SESSION_EXPANDED],
				array_search(
					$id,
					$_SESSION[SESSION_EXPANDED]
				),
				1
			);
		} else {
			// To expand, add id to list of expanded ids
			$_SESSION[SESSION_EXPANDED][] = $id;
		}
		// Back to list view
		$this->page->redirect_url($this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$owner->_id, PARAM_LIST=>$listInfo->_name)).'#'.ID_PREFIX_A.$id, true);
		exit;
	}


	function backup() {
		$mysqldump = $this->config->value(CONFIG_CMS_PATH_MYSQLDUMP, PATH_MYSQLDUMP.'mysqldump');
		if (is_executable($mysqldump)) {
			$backup = $this->content->backup($mysqldump);
			if (is_string($backup)) {
				$backup = gzencode($backup);
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.strtolower($this->content->getSiteName()).'-backup-'.date('Ymd').'.gz');
				print $backup;
			} else {
				$this->error(
					sprintf($this->lang->l('error.backup'), $backup),
					$this->lang->l('error.askforsupport'),
					$this->page->link()
				);
			}
		} else {
			$this->error(
				sprintf($this->lang->l('error.mysqldumpnotexecutable'), $mysqldump),
				false,
				$this->page->link()
			);
		}
		exit;
	}


	/**
	 * Print the navigation.
	 */
	function _printNavigation() {
		// Don't show navigation if we were called from another window
		if ($this->_getParam(PARAM_CALLING_FIELD, true)) {
			return;
		}

		// Check whether we have to expand or collapse some navigation item
		$prefix = $this->_getParam(PARAM_EXPAND, true);
		if ($prefix) {
			if (in_array($prefix, $_SESSION[SESSION_EXPANDED])) {
				// To collapse, delete prefix from list of expanded ids
				array_splice(
					$_SESSION[SESSION_EXPANDED],
					array_search(
						$prefix,
						$_SESSION[SESSION_EXPANDED]
					),
					1
				);
			} else {
				// To expand, add prefix to list of expanded ids
				$_SESSION[SESSION_EXPANDED][] = $prefix;
			}
		}

		// Print navigation
		$id = $this->_getParam(PARAM_ID, true);
		$list = $this->_getParam(PARAM_LIST, true);
		$owner = $this->_getParam(PARAM_OWNER, true);
		if (!$lists) {
			$lists = $this->content->getLists(SITE);
		}
		print '<div id="navigation">'."\n";
		$this->page->table_head(array(ucfirst($this->lang->l('contentstructure')), 250), 'navigation');
		$this->_printNavigationEntry('<a href="'.SCRIPTNAME.'">'.$this->lang->l('welcomehead').'</a>', 0, !$id && !$list && !$owner);
		$oldPrefixes = array();
		$oldPrefix = false;
		$level = 0;
		$maxVisibleLevel = $level;
		foreach ($lists as $listInfo) {
			$prefixes = preg_split('/([A-Z][a-z]*)/', $listInfo->_name, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$prefix = implode('', array_slice($prefixes, 0, -1));
			if ($prefix!=$oldPrefix) {
				if (count($prefixes)<=count($oldPrefixes)) {
					$level--;
					$maxVisibleLevel = min($maxVisibleLevel, $level);
				}
				if (count($prefixes)>=count($oldPrefixes)) {
					$expanded = in_array($prefix, $_SESSION[SESSION_EXPANDED]);
					if ($level<=$maxVisibleLevel) {
						$this->_printNavigationEntry(ucfirst($this->lang->l(LANG_PREFIX_LIST.$prefix)), $level, false, true, $expanded, $prefix);
					}
					$level++;
					if ($expanded && $level-1==$maxVisibleLevel) {
						$maxVisibleLevel = $level;
					}
				}
				$oldPrefixes = $prefixes;
				$oldPrefix = $prefix;
			}
			if ($level>$maxVisibleLevel) {
				continue;
			}
			if ($listInfo->_singleton) {
				// Try to retrieve the atom
				$atoms = $this->content->getAtoms(SITE_ID, $listInfo);
				if (count($atoms)>0) {
					// If there's an atom, make an edit link
					$link = $this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_ID=>$atoms[0]->_id));
					// Check if there are sub-lists and print these
					$childListButtons = array();
					$childListButtonsThisIsMe = array();
					$childLists = $this->content->getLists($listInfo->_target);
					if (count($childLists)>0) {
						foreach ($this->content->getLists($listInfo->_target) as $targetList) {
							$childListButtonsThisIsMe[] = $owner==$atoms[0]->_id && $targetList->_name==$list;
							$childListButtons[] = '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>$atoms[0]->_id, PARAM_LIST=>$targetList->_name)).'" title="'.sprintf($this->lang->l('editlist'), $this->lang->l(LANG_PREFIX_LIST.$targetList->_name)).'">'.ucfirst($this->lang->l(LANG_PREFIX_LIST.$targetList->_name))."</a>";
						}
					}
					$selected = $atoms[0]->_id==$id;
				} else {
					// If there's no atom, make a create link
					$link = $this->page->link(array(PARAM_ACTION=>ACTION_EDIT, PARAM_OWNER=>SITE_ID, PARAM_ID=>ID_NEW, PARAM_LIST=>$listInfo->_name, PARAM_ORDER=>1));
					$childListButtons = array();
					$selected = $owner==SITE_ID && $list==$listInfo->_name;
				}
				$subListsExpandable = count ($childListButtons)>0;
				$subListsExpanded = in_array($listInfo->_name, $_SESSION[SESSION_EXPANDED]);
				$this->_printNavigationEntry('<a href="'.$link.'" title="'.sprintf($this->lang->l('editsingleton'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name)).'">'.ucfirst($this->lang->l(LANG_PREFIX_LIST.$listInfo->_name)).'</a>', $level, $selected, $subListsExpandable, $subListsExpanded, $listInfo->_name);
				if ($subListsExpandable && $subListsExpanded && $level<=$maxVisibleLevel) {
					foreach ($childListButtons as $number=>$childListButton) {
						$this->_printNavigationEntry($childListButton, $level+1, $childListButtonsThisIsMe[$number]);
					}
				}
			} else {
				$this->_printNavigationEntry('<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_VIEW, PARAM_OWNER=>SITE_ID, PARAM_LIST=>$listInfo->_name)).'" title="'.sprintf($this->lang->l('editlist'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name)).'">'.ucfirst($this->lang->l(LANG_PREFIX_LIST.$listInfo->_name)).'</a>', $level, $owner==SITE_ID && $list==$listInfo->_name);
			}
		}
		$this->page->table_foot();
		print "</div>\n";
	}


	/**
	 * Print a navigation entry.
	 *
	 * @param string $link The text to be used as link (enclosed in <code>&lt;a<&gt;</code>)
	 * @param int $level The indention level
	 * @param boolean $selected Flag whether to mark this entry as selected
	 */
	function _printNavigationEntry($link, $level, $selected = false, $expandable = false, $expanded = false, $expandPrefix = false) {
		$expandOperation = $expanded ? 'collapse' : 'expand';
		$expandTitle = ucfirst(sprintf($this->lang->l($expandOperation.'childs'), $this->lang->l(LANG_PREFIX_LIST.$listInfo->_name)));
		$expandUrl = SCRIPTNAME.'?';
		foreach ($_REQUEST as $name=>$value) {
			if ($name!=PARAM_EXPAND && $name!=session_name()) {
				$expandUrl .= '&'.$name.'='.rawurlencode($value);
			}
		}
		$expandUrl .= '&'.PARAM_EXPAND.'='.$expandPrefix.'#'.$expandPrefix;
		$firstPrefix = '<img src="elements/blank.gif" width="'.($level*WIDTH_INDENT_NAVIGATION).'" height="20" alt="" />'
			.($expandable
				? '<a href="'.$expandUrl.'" title="'.$expandTitle.'"><img src="elements/'.$expandOperation.'.gif" alt="'.$expandTitle.'"></a>'
				: '<img src="elements/blank.gif" alt="" />'
			)
			.'<img src="elements/blank.gif" width="10" height="20" alt="" />';
		if ($expandable) {
			$link = '<a name="'.$expandPrefix.'">'.$link.'</a>';
		}
		print $this->page->table_row(array($firstPrefix.$link), false, false, $selected ? 'style="background-color:'.$this->config->value(CONFIG_CMS_COLOR_HIGHLIGHT).';"' : false);
	}


	/**
	 * Generate all slugs
	 */
	function generateSlugs() {
		$this->content->generateSlugs();
	}


	/**
	 * Show the main menu of the CMS, called "Welcome page".
	 *
	 */
	function showWelcome() {
		$this->page->open($this->lang->l('welcomehead'), $this->lang->l('welcomehead'));

		print "<div id=\"container\">\n";
		$this->_printNavigation();
		print '<div id="page">';

		$this->page->p(sprintf($this->lang->l('welcome'), $this->lang->l('cms').' '.$this->lang->l('of').' <strong>'.$this->content->getSiteName().'</strong>'));
		$this->page->p(sprintf($this->lang->l('today'), date($this->lang->l('date'))).' &ndash; '.sprintf($this->lang->l('youare'), '<strong>'.$this->getUsername().'</strong>'));

		if (!$this->config->value(CONFIG_CMS_FIXED_LANGUAGE, false)) {
			$options = array();
			foreach ($this->lang->getAvailableLanguages() as $language) {
				$options[$language] = $this->lang->l(LANG_PREFIX_LANGUAGE.$language);
			}
			asort($options);
			$selectLanguage = $this->page->input_select('language', $options, $this->lang->getSelectedLanguage(), 1, false, "location.href='?lang='+this.value");
			$this->page->p($this->lang->l('selectlanguage').': '.$selectLanguage);
		}

		$this->page->p($this->lang->l('possibilities'));

		$mysqldump = $this->config->value(CONFIG_CMS_PATH_MYSQLDUMP, PATH_MYSQLDUMP.'mysqldump');
		if (is_executable($mysqldump)) {
			$this->page->p(sprintf($this->lang->l('backupnote'), '<a href="'.$this->page->link(array(PARAM_ACTION=>ACTION_BACKUP)).'" title="'.sprintf($this->lang->l('downloadbackup'), $this->lang->l('backupdatabase')).'">'.$this->lang->l('backupdatabase').'</a>'));
		}

		$this->page->p(sprintf($this->lang->l('logoutnote'), '<a href="javascript:window.close();">'.$this->lang->l('closewindow').'</a>', $this->lang->l('cms')));

		print '</div>';
		print '</div>';
		$this->page->close();
	}


	function getUsername() {
		return $this->uid;
	}


	function _checkPassword($uid, $password) {
		// Check whether a user was specified in the config file
		if ($this->config->value(CONFIG_CMS_USERNAME, uniqid('cmslogin_'))==$uid) {
			return $this->config->value(CONFIG_CMS_PASSWORD)==$password;
		} else {
			// Check whether there was an ldap connection specified
			$ldapServer = $this->config->value(CONFIG_LDAP_SERVER);
			if ($ldapServer) {
				// LDAP server was specified, use this
				$ldap = ldap_connect($ldapServer);
				if ($ldap) {
					$r = ldap_bind($ldap)
						? @ldap_compare($ldap, "CN=$uid,".$this->config->value(CONFIG_LDAP_BASEDN), 'userPassword', $password)
						: false;
					ldap_close($ldap);
					return $r===true;
				} else {
					return false;
				}
			} else {
				// No LDAP server was specified, use the CMS's users table
				return $this->content->checkPassword($uid, $password);
			}
		}
	}

	function checkLogin() {
		if (!$_SESSION[SESSION_EXTERNALLOGIN]) {
			// Perform HTML authentication
			if (isset($_REQUEST[PARAM_LOGIN])) {
				$_SESSION[SESSION_USERNAME] = $_POST[PARAM_USERNAME];
				$_SESSION[SESSION_PASSWORD] = $_POST[PARAM_PASSWORD];
			}

			if (!$this->_checkPassword($_SESSION[SESSION_USERNAME], $_SESSION[SESSION_PASSWORD])) {
				$this->page->page_preface($this->lang->l('login'));
				$this->page->page_head($this->lang->l('logintocms'));
				print '<p>'.$this->lang->l('loginneeded')."</p>\n";
				print '<p>'.$this->lang->l('enterlogin').":</p>\n";
				$this->page->form_head();
				$this->page->hidden('login',1);
				$this->page->table_head(array($this->lang->l('login'), 200, '', 0), 'login');
				$this->page->table_row(array($this->lang->l('username').':', $this->page->input_text('username', '', 20)));
				$this->page->table_row(array($this->lang->l('password').':', $this->page->input_password('password', 20)));
				$this->page->table_foot();
				$this->page->form_foot(false);
				$this->page->close();
				exit;
			}

			if (isset($_REQUEST['login'])) {
				// Login was ok, jump to the CMS start page
				$this->page->redirect_url($this->page->link());
				exit;
			}
		}

		$this->uid = $_SESSION[SESSION_USERNAME];
	}


	/**
	 * Check the server features for compatibility.
	 */
	function checkServer() {
		$extensions = get_loaded_extensions();
		if (!is_integer(array_search('mysqli', $extensions))) {
			$error = $this->lang->l('error.nomysql');
		} else if (!is_integer(array_search('gd', $extensions))) {
			$error = $this->lang->l('error.nogd');
		} else if (version_compare(phpversion(), CMS_REQUIRED_PHP_VERSION)<0) {
			$error = sprintf($this->lang->l('error.phpversiontoolow'), CMS_REQUIRED_PHP_VERSION, phpversion());
		} else {
			$error = false;
		}
		if ($error) {
			$this->error(
				$error,
				$this->lang->l('error.cannotwork')
			);
			exit;
		}
	}


	function error($msg, $msgMore = false, $backUrl = false) {
		if ($this->page) {
			$this->page->error($msg, $msgMore, $backUrl);
		} else {
			print '<p><strong>'.$this->lang->l('error.error').'</strong>: '.$msg.'</p>';
			if ($msgMore) {
				print '<p>'.$msgMore.'</p>';
			}
		}
	}


	/**
	 * "Run" the CMS.
	 *
	 */
	function run() {
		// Read the config file
		$configFile = PATH_PREFIX.PATH_CONFIG.PATH_CONFIG_FILE;
		if (is_readable($configFile)) {
			$this->config = new config($configFile);
		} else {
			$this->error(
				sprintf('Cannot read the configuration file %s.', "'$configFile'"),
				'The redaction system cannot work due to this error.'
			);
			exit;
		}

		// Determine the GUI language
		$language = $this->config->value(CONFIG_CMS_FIXED_LANGUAGE, false);
		if (!$language) {
			$language = $this->_getParam('lang', true);
			if (!$language) {
				$language = $_COOKIE['cms_language'];
			}
			if ($language) {
				// Save the language selection
				setcookie('cms_language', $language, time()+60*60*24*365);
			}
		}
		$this->lang = new lang($language);

		// Create the content access object
		$siteFile = PATH_PREFIX.PATH_CONFIG.$this->config->value(CONFIG_CMS_SITE_FILE, PATH_SITE_FILE);
		$notReadable = false;
		if (is_readable($siteFile)) {
			$this->content = new content(
				$siteFile,
				$this->config->value(CONFIG_DATABASE_HOSTNAME),
				$this->config->value(CONFIG_DATABASE_DATABASE),
				$this->config->value(CONFIG_DATABASE_USERNAME),
				$this->config->value(CONFIG_DATABASE_PASSWORD),
				$this->config->value(CONFIG_CMS_TABLEPREFIX, false)
			);
			if (!$this->content->site) {
				$notReadable = true;
			}
			if (!$this->content->isConnected()) {
				$this->error(
					$this->lang->l('error.notconnected'),
					$this->lang->l('error.cannotwork')
				);
				exit;
			}
		} else {
			$notReadable = true;
		}
		if ($notReadable) {
			$this->error(
				sprintf($this->lang->l('error.sitefilenotreadable'), "'$siteFile'"),
				$this->lang->l('error.cannotwork')
			);
			exit;
		}
		$this->content->setSelectedLanguage($this->lang->getSelectedLanguage());

		// Start session
		session_name('cms');
		session_start();

		// Get or set the root ID
		$rootId = $this->_getParam(PARAM_ROOT, true);
		if ($rootId) {
			$_SESSION[SESSION_ROOT] = $rootId;
		} else {
			if (!isset($_SESSION[SESSION_ROOT])) {
				$_SESSION[SESSION_ROOT] = false;
			}
		}
		$this->rootId = $_SESSION[SESSION_ROOT];

		// Create an output page
		$this->page = new page(
			$this->content->getSiteName(),
			$this->lang,
			$this->config
		);

		// Check the login
		$this->checkLogin();
		$this->content->setUsername($this->getUsername());

		// Check the server configuration
		$this->checkServer();

		// Parse the defined additional plugin files
		foreach (explode(ID_SEPARATOR, $this->config->value(CONFIG_CMS_LISTENER_FILES)) as $listenerFile) {
			if ($listenerFile!='') {
				$listenerFile = PATH_PREFIX.$listenerFile;
				if (is_readable($listenerFile)) {
					include($listenerFile);
				} else {
					$this->error(
						sprintf($this->lang->l('error.listenerfilenotreadable'), "'$listenerFile'"),
						$this->lang->l('error.cannotwork')
					);
					exit;
				}
			}
		}

		// Parse the definited additional actions
		foreach (explode(ID_SEPARATOR, $this->config->value(CONFIG_CMS_ACTIONS)) as $action) {
			// Try to open the additional file
			if (preg_match('|([a-z\./]*/)?([a-z\.]+)(\[([a-z;]+)\])?|', strtolower($action), $matches)) {
				// Get the atom types this action is specified on
				$actionPath = $matches[1];
				$action = $matches[2];
				$lists = $matches[4];
				$actionFile = $actionPath.$action.'.inc.php';
				if (is_readable($actionFile)) {
					// Load and register this action
					include($actionFile);
					$this->actions[$action] = eval("return new $action;");
					$this->actionPaths[$action] = $actionPath;
					// Check the methods
					foreach (array('handleaction') as $method) {
						if (!method_exists($this->actions[$action], $method)) {
							$this->error(
								sprintf($this->lang->l('error.actionmethodmissing'), "'$actionFile'"),
								$this->lang->l('error.cannotwork')
							);
							exit;
						}
					}
					// Load language strings for the action
					$this->lang->loadStrings($actionPath, $action.'_%s.ini');
					// Bind the action to the requested atom types
					foreach (explode(';', $lists) as $list) {
						$this->actionsByList[$list][] = $action;
					}
				} else {
					$this->error(
						sprintf($this->lang->l('error.actionfilenotreadable'), "'$actionFile'"),
						$this->lang->l('error.cannotwork')
					);
					exit;
				}
			}
		}

		// Initialize the expanded status
		if (!isset($_SESSION[SESSION_EXPANDED])) {
			$_SESSION[SESSION_EXPANDED] = array();
		}

		$action = $this->_getParam(PARAM_ACTION, true);
		switch ($action) {
			case ACTION_VIEW:
				$this->showListPOST();
				break;
			case ACTION_EDIT:
				$this->showEditMaskPOST();
				break;
			case ACTION_SAVE:
				$this->saveAtomPOST();
				break;
			case ACTION_DELETE:
				$this->deleteAtomPOST();
				break;
			case ACTION_UP:
				$this->moveAtomPOST(-1);
				break;
			case ACTION_DOWN:
				$this->moveAtomPOST(1);
				break;
			case ACTION_EXPAND:
				$this->expandPOST();
				break;
			case ACTION_BACKUP:
				$this->backup();
				break;
			case ACTION_METADATA:
				$this->editMetadata();
				break;
			case ACTION_GENERATESLUGS:
				$this->generateSlugs();
				break;
			default:
				// Try one of the defined actions
				if (isset($this->actions[$action])) {
					$actionObject = $this->actions[$action];
					$actionObject->handleAction($this->content->getAtom($this->_getParam(PARAM_ID)), $this);
				} else if (!$this->rootId) {
					// Show welcome page only when not called externally
					$this->showWelcome();
				}
		}
	}


}	// End of class



// Create and run
$content = new cms();
$content->run();



?>
