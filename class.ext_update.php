<?php
/***************************************************************
*  Copyright notice
*
*  (c)   2004-2005 Rupert Germann <rupi@gmx.li>
*  All   rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Class for updating tx_skbookreview_books_category_mm.
 *
 * @author Steffen Kamper <steffen@dislabs.de>
 * @package TYPO3
 * @subpackage sk_bookreviews
 */
class ext_update {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		// check only for events without calendar_id
		// TODO check if a category has multiple fe_users or fe_groups assigend to itself
		if ($testres && $GLOBALS['TYPO3_DB']->sql_num_rows($testres)) {
			$returnthis='<h3>No Update needed!</h3>';
		} else {
			$onClick = "document.location='".t3lib_div::linkThisScript(array('dotheupdate' => 1))."'; return false;";
			$do=t3lib_div::_GP('dotheupdate');
			if($do) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('*', 'tx_skbookreview_books', '');
				while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$cats=explode(',',$row['category']);
					$sort=1;
					foreach($cats as $cat) {
						$insert=Array(
						'uid_local' => $row['uid'],
						'uid_foreign' => $cat,
						'sorting'=>$sort++,
						);
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_skbookreview_books_category_mm',$insert);
					}
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_skbookreview_books','uid='.$row['uid'],array('category'=>--$sort));
				}
				$returnthis='<h3>Update Done!<br>please clear cache now.</h3>';
			} else {
				$returnthis='<form action="">';
				$returnthis.='';
				$returnthis.='<input type="submit" value="update now!" onclick="'.htmlspecialchars($onClick).'">';
				$returnthis.='</form>';
			}
		}
		
		
		return $returnthis;
	}

	function access($what = 'all') {
		
		
		$testres = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('count(*)', 'tx_skbookreview_books_category_mm', '');
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($testres); 
		return ($row[0]==0);
		
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/class.ext_update.php']);
}
?>
