<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_skbookreview_category=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_skbookreview_pi1 = < plugin.tx_skbookreview_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_skbookreview_pi1.php','_pi1','list_type',1);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_skbookreview_books'][0] = array(
    'fList' => 'cover,title,author,category;new',
    'icon' => TRUE
);


?>