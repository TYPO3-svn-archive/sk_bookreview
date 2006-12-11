<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages("tx_skbookreview_books");


t3lib_extMgm::addToInsertRecords("tx_skbookreview_books");

$TCA["tx_skbookreview_books"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"thumbnail" => "cover",
		"default_sortby" => "ORDER BY date",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_skbookreview_books.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, category, cover, author, publisher, additional, level, link, points, impression, description, result, pages, price, isbn, date, buylink, reviewer, clicks",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_skbookreview_category");

$TCA["tx_skbookreview_category"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_category',		
		'label' => 'category',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
        "default_sortby" => "ORDER BY crdate",	
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_skbookreview_category.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "category",
	)
);


if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txskbookreviewM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:sk_bookreview/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Book Review");

# einblenden pi_flexform
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';

# Wir definieren die Datei, die unser Flexform Schema enthält
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_bookreview.xml');


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_skbookreview_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_skbookreview_pi1_wizicon.php';
?>
