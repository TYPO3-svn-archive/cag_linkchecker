<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$_EXTCONF = unserialize($_EXTCONF);	// unserializing the configuration so we can use it here:
if ($_EXTCONF['setPageTSconfig'])	{
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cag_linkchecker/res/pageTSconfig.txt">');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks']['db'] = 'EXT:cag_linkchecker/lib/class.tx_caglinkchecker_checkinternallinks.php:tx_caglinkchecker_checkinternallinks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks']['file'] = 'EXT:cag_linkchecker/lib/class.tx_caglinkchecker_checkfilelinks.php:tx_caglinkchecker_checkfilelinks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks']['external'] = 'EXT:cag_linkchecker/lib/class.tx_caglinkchecker_checkexternallinks.php:tx_caglinkchecker_checkexternallinks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks']['linkhandler'] = 'EXT:cag_linkchecker/lib/class.tx_caglinkchecker_checklinkhandlerlinks.php:tx_caglinkchecker_checklinkhandlerlinks';

/*
if ($_EXTCONF['setUserTSconfig']) {
	t3lib_extMgm::addUserTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:###extension_key###/userTSconfig.txt">');
}
*/
?>