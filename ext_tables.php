<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE') {

		// add module

	t3lib_extMgm::addModulePath('web_txcaglinkcheckerM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('web', 'txcaglinkcheckerM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

}
?>
