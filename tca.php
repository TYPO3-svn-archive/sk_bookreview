<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_skbookreview_books"] = Array (
	"ctrl" => $TCA["tx_skbookreview_books"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,category,cover,author,publisher,additional,level,link,points,impression,description,result,pages,price,isbn,date,buylink,reviewer,clicks"
	),
	"feInterface" => $TCA["tx_skbookreview_books"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"category" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.category",
			"config" => Array (
				"type" => "select",
				"items" => Array (

				),
				"foreign_table" => "tx_skbookreview_category",
				"foreign_table_where" => "AND tx_skbookreview_category.pid=###CURRENT_PID### ORDER BY tx_skbookreview_category.uid",
				"size" => 6,
				"minitems" => 0,
				"maxitems" => 10,
				"multiple" => 1,
				"MM" => "tx_skbookreview_books_category_mm",
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_skbookreview_category",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"cover" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.cover",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 100,
				"uploadfolder" => "uploads/tx_skbookreview",
				"show_thumbs" => 1,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"author" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.author",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"publisher" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.publisher",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"additional" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.additional",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"level" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.level",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.level.I.0", "0", t3lib_extMgm::extRelPath("sk_bookreview")."res/selicon_tx_skbookreview_books_level_0.gif"),
					Array("LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.level.I.1", "1", t3lib_extMgm::extRelPath("sk_bookreview")."res/selicon_tx_skbookreview_books_level_1.gif"),
					Array("LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.level.I.2", "2", t3lib_extMgm::extRelPath("sk_bookreview")."res/selicon_tx_skbookreview_books_level_2.gif"),
					Array("LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.level.I.3", "3", t3lib_extMgm::extRelPath("sk_bookreview")."res/selicon_tx_skbookreview_books_level_3.gif"),
				),
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"link" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.link",
			"config" => Array (
				"type" => "input",
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"points" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.points",
			"config" => Array (
				"type" => "input",
				"size" => "5",
				"max" => "1",
			)
		),
		"impression" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.impression",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"result" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.result",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"pages" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.pages",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"price" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.price",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"isbn" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.isbn",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"date" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.date",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"buylink" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.buylink",
			"config" => Array (
				"type" => "input",
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"reviewer" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_books.reviewer",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"clicks" => Array (
			"config" => Array (
				"type" => "passthrough",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, category;;;;3-3-3, cover, author, publisher, additional, level, link, points, impression, description;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], result;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], pages, price, isbn, date, buylink, reviewer, clicks")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_skbookreview_category"] = Array (
	"ctrl" => $TCA["tx_skbookreview_category"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "category"
	),
	"feInterface" => $TCA["tx_skbookreview_category"]["feInterface"],
	"columns" => Array (
		"category" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:sk_bookreview/locallang_db.xml:tx_skbookreview_category.category",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "category;;;;1-1-1")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>