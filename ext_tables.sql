#
# Table structure for table 'tx_skbookreview_books_category_mm'
# 
#
CREATE TABLE tx_skbookreview_books_category_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_skbookreview_books'
#
CREATE TABLE tx_skbookreview_books (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	category int(11) DEFAULT '0' NOT NULL,
	cover blob NOT NULL,
	author tinytext NOT NULL,
	publisher tinytext NOT NULL,
	additional tinytext NOT NULL,
	level int(11) DEFAULT '0' NOT NULL,
	link tinytext NOT NULL,
	points tinytext NOT NULL,
	impression text NOT NULL,
	description text NOT NULL,
	result text NOT NULL,
	pages tinytext NOT NULL,
	price tinytext NOT NULL,
	isbn tinytext NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	buylink tinytext NOT NULL,
	reviewer tinytext NOT NULL,
	clicks tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_skbookreview_category'
#
CREATE TABLE tx_skbookreview_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	category tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
