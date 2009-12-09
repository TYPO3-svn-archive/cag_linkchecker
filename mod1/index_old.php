<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 - 2009 Jochen Rieger (j.rieger@connecta.ag)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Linkchecker' for the 'cag_linkchecker' extension.
 *
 * @author	Jochen Rieger <j.rieger@connecta.ag>
 */



// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:cag_linkchecker/mod1/locallang.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');

// Check user permissions
$BE_USER->modAccess($MCONF, 1);
// DEFAULT initialization of a module [END]

class tx_caglinkchecker_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $pid; // Id of actual page
	var $linkWhere = 'bodytext LIKE \'%<LINK http:%\' AND deleted = 0';



	/**
	 * Initialization of the class
	 *
	 * @return		Void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->pid = t3lib_div::_GP('id') > 0 ? t3lib_div::_GP('id') : 0;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

			// Draw the header.
			$this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

			// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);

			// Render content:
			$this->moduleContent();


			// ShortCut
			#if ($BE_USER->mayMakeShortcut())	{
			#	$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			#}

			$this->content.=$this->doc->spacer(5);

		} else {

			// If no access or if ID == zero
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}


	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'0' => $LANG->getLL('menu.introduction'),
				'1' => $LANG->getLL('menu.overview'),
				'2' => $LANG->getLL('menu.checkLinks'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Generates the module content
	 */
	function moduleContent() {
		global $LANG;

		// Start section
		$this->content .= $this->doc->sectionBegin();

		switch((int)$this->MOD_SETTINGS['function'])	{
			case 2:
				$this->content.= $this->displayOverview();

				$sContent = $this->showBrokenLinksTableFromBranch($this->getPidList($this->pid));
				$this->content.= $this->doc->section($LANG->getLL('list.header'),$sContent,0,1);
			break;
			case 1:
				$this->content.= $this->displayOverview();
				$this->content.= $this->doc->sectionHeader($LANG->getLL('overview.attention.header'));
				$this->content.= $LANG->getLL('overview.attention.text');
			break;
			default:
				$this->content.= $this->displayIntroduction();
				$this->content.= $this->doc->sectionHeader($LANG->getLL('overview.attention.header'));
				$this->content.= $LANG->getLL('overview.attention.text');
			break;
		}

		// End section
		$this->content .= $this->doc->sectionEnd();
	}
	
	
	function displayIntroduction() {
		
		// The array to put the content into
		$html = array();
		
		$html[] = 'ADD SOME INTRO CONTENT';
		
		$html[] = $this->doc->spacer(5);
		
		// Return the table html code as string
		return implode(chr(10),$html);
		
	} // end function displayIntroduction()


	function displayOverview() {
		global $LANG;

		// The array to put the content into
		$html = array();

		$sContent = $LANG->getLL('overview.all.records').$this->getAmountOfRecWithExtLinks().'<br />';
		$sContent.= $LANG->getLL('overview.all.links').$this->getAmountOfExtLinks();
		$html[] = $this->doc->section($LANG->getLL('overview.all.header'),$sContent,0,1);

		$html[] = $this->doc->spacer(5);

		$sContent = $LANG->getLL('overview.branch.records').$this->getAmountOfRecWithExtLinks($this->getPidList($this->pid)).'<br />';
		$sContent.= $LANG->getLL('overview.branch.links').$this->getAmountOfExtLinks($this->getPidList($this->pid));
		$html[] = $this->doc->section($LANG->getLL('overview.branch.header'),$sContent,0,1);

		$html[] = $this->doc->spacer(5);

		// Return the table html code as string
		return implode(chr(10),$html);
	}

	function showBrokenLinksTableFromBranch($pidList, $table = 'tt_content') {
		$content = $this->startTable();
		$switch = true;

		// Where statement for database query
		if ($pidList != '') {
			$where = 'pid IN ('.$pidList.') AND ';
		}
		$where.= $this->linkWhere;

		// Select content elements that contain external links
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid, header, bodytext',$table,$where);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// Extract external links from bodytext into array $urls
			$urls = array();
			preg_match_all('/((?:http|https|ftp|ftps))(?::\/\/)(?:[^\s<>]+)/i', $row['bodytext'], $urls, PREG_PATTERN_ORDER);

			for ($i = 0; $i < sizeof($urls[0]); ++$i) {
				$checkURL = $this->checkURL($urls[0][$i]);
				if ($checkURL != 1) {
					$content .= $this->getRow($row['uid'], $row['pid'], $row['header'], $urls[0][$i], $checkURL, $table, $switch);
				}
			}
		}

		// external page links
		if ($pidList != '') {
			$where = 'uid IN ('.$pidList.') AND ';
		}
		$where .= 'doktype = 3 && urltype != 3';

		// Select pages which are linking to external pages
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid, title, url, urltype', 'pages', $where);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['urltype'] == 1) {
				$row['url'] = 'http://' . $row['url'];
			} elseif ($row['urltype'] == 2) {
				$row['url'] = 'ftp://' . $row['url'];
			} elseif ($row['urltype'] == 4) {
				$row['url'] = 'https://' . $row['url'];
			}

			$checkURL = $this->checkURL($row['url']);
			if ($checkURL != 1) {
				$content .= $this->getRow($row['uid'], $row['pid'], $row['title'], $row['url'], $checkURL, 'pages', $switch);
			}
		}

		return $content;
	}

	function startTable() {
		global $LANG;

		// Listing head
		$html = array();
		$html[] = $this->doc->sectionHeader($LANG->getLL('list.header'));
		$html[] = $this->doc->spacer(5);
		$html[] = '<table id="brokenLinksList" border="0" width="100%" cellspacing="1" cellpadding="3" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
		$html[] = '<tr>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.path').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.headline').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.linktarget').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.linkmessage').'</b></td>';
		$html[] = '<td class="head" align="center"></td>';
		$html[] = '</tr>';

		return implode(chr(10), $html);
	}

	function getRow($uid, $pid, $header, $url, $response, $table, &$switch) {
		$html = array();
		$params = '&edit['.$table.']['.$uid.']=edit';
		$actionLinks = '<a href="#" onclick="'.t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'],'').'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif','width="11" height="12"').' title="edit" alt="edit" /></a>';

		//Alternating row colors
         if ($switch == true){
             $switch = false;
             $html[] = '<tr bgcolor="'.$this->doc->bgColor3.'">';
         } elseif($switch == false){
              $switch = true;
              $html[] = '<tr bgcolor="'.$this->doc->bgColor5.'">';
   		 }

		$html[] = '<td class="content">'.t3lib_BEfunc::getRecordPath($pid,'',0,0).'</td>';
		$html[] = '<td class="content">'.$header.'</td>';
		$html[] = '<td class="content"><a href="'.$url.'" target="_blank">'.$url.'</a></td>';
		$html[] = '<td class="content">'.$response.'</td>';
		$html[] = '<td class="content">'.$actionLinks.'</td>';
		$html[] = '</tr>';

		// Return the table html code as string
		return implode(chr(10),$html);
	}

	function getAmountOfRecWithExtLinks($pidList = '', $table = 'tt_content') {

		// Where statement of SQL query
		$where = '';
		if ($pidList != '') {
			$where.= 'pid IN ('.$pidList.') AND ';
		}
		$where.= $this->linkWhere;

		// Count external links
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) AS amount',$table,$where);

		// Get record rows
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Return amount of records that contain external links
		return $row['amount'];

	} // end getTotalAmountOfExternalLinks()


	function getAmountOfExtLinks($pidList = '', $table = 'tt_content') {

		// Return value (amount of external links)
		$links = 0;
		// Array to store the links per cObj
		$urls = array();

		// Where statement of SQL query
		$where = '';
		if ($pidList != '') {
			$where.= 'pid IN ('.$pidList.') AND ';
		}
		$where.= $this->linkWhere;

		// Select records that contain external links
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid, header, bodytext',$table,$where);

		// Get record rows
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			// Extract external links from bodytext into array $urls
			preg_match_all("/(?:<link http|<link https)(?::\/\/)(?:[^\s<>]+)/i", $row['bodytext'], $urls, PREG_PATTERN_ORDER);

			// Add amount of links in cObj
			$links+= sizeof($urls[0]);
		}

		// Return the cumulated amount of external links in $table
		return $links;
	}


	/**
	 * Wraps an edit link around a string.
	 * Creates a page module link for pages, edit link for other tables.
	 *
	 * @param	string		The String to be wrapped
	 * @param	string		Table name (tt_content,...)
	 * @param	integer		uid of the record
	 * @return	string		Rendered Link
	 */
	function wrapEditLink($str, $table, $id) {
		global $BACK_PATH;

		if ($table == 'pages') {
			$editOnClick = "top.fsMod.recentIds['web'] = " . $id . ";";
			$editonClick.= "top.goToModule('web_layout', 1);";
		} else {
			$params = '&edit['.$table.']['.$id.']=edit';
			$editOnClick = t3lib_BEfunc::editOnClick($params, $BACK_PATH);
		}
		return '<a href="#" onClick="'.htmlspecialchars($editOnClick).'">'.$str.'</a>';
		
	} // end function wrapEditLink


	/**
	 * Gets a list pages that belong to $pid
	 *
	 * @param	integer		ID of page (start of branch)
	 * @return	string		Comma separated list with all IDs belongin to $pid
	 */
	function getPidList($pid) {

		// Pidlist (comma separated) that is returned
		$pidList = $pid;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'pages', 'pid = '.$pid.' AND deleted = 0');

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$pidList.= ','.$this->getPidList($row['uid']);
		}

		return $pidList;
	
	} // end function getPidList($pid)


	/**
	 * Checks a given URL + /path/filename.ext for validity
	 *
	 * @param	string		complete URL - Example: 'http://www.domain.com/...'
	 * @return	int|string		1 if anything went well or an error message
	 */
	function checkURL($url) {
		// remove possible anchor from the url
		if (strrpos($url, '#') !== false) {
			$url = substr($url, 0, strrpos($url, '#'));
		}

		// try to fetch the content of the URL (just fetching of headers doesn't work!)
		$report = array();
		t3lib_div::getURL($url, 1, false, $report);

		// analyze the response
		if ($report['error']) {
			return $report['lib'] . ': (' . $report['error'] . ') ' . $report['message'];
		}

		return 1;
	}
} // end class


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_caglinkchecker_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
