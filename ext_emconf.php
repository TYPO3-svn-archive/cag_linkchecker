<?php

########################################################################
# Extension Manager/Repository config file for ext: "cag_linkchecker"
#
# Auto generated 09-12-2009 20:44
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Linkchecker',
	'description' => 'A backend module that checks all kinds of links on your website for validity. Originally developed for Connecta AG, Wiesbaden.',
	'category' => 'module',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Jochen Rieger / Dimitri König',
	'author_email' => 'j.rieger@connecta.ag',
	'author_company' => 'Connecta AG / cab services ag',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.1.0-0.0.0',
			'php' => '5.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:9:"ChangeLog";s:4:"f7a0";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"6360";s:12:"ext_icon.gif";s:4:"3cd3";s:17:"ext_localconf.php";s:4:"8c4f";s:14:"ext_tables.php";s:4:"f4fc";s:13:"locallang.xml";s:4:"a34b";s:14:"doc/manual.sxw";s:4:"067e";s:19:"doc/wizard_form.dat";s:4:"2485";s:20:"doc/wizard_form.html";s:4:"b4cb";s:50:"lib/class.tx_caglinkchecker_checkexternallinks.php";s:4:"c374";s:46:"lib/class.tx_caglinkchecker_checkfilelinks.php";s:4:"ea35";s:50:"lib/class.tx_caglinkchecker_checkinternallinks.php";s:4:"2005";s:53:"lib/class.tx_caglinkchecker_checklinkhandlerlinks.php";s:4:"c1bb";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"8070";s:14:"mod1/index.php";s:4:"e1e5";s:18:"mod1/index_old.php";s:4:"8ed5";s:18:"mod1/locallang.xml";s:4:"f40b";s:22:"mod1/locallang_mod.xml";s:4:"33b2";s:22:"mod1/mod_template.html";s:4:"ab33";s:19:"mod1/moduleicon.gif";s:4:"c50f";s:19:"res/linkchecker.css";s:4:"4696";s:20:"res/pageTSconfig.txt";s:4:"9256";}',
	'suggests' => array(
	),
);

?>
