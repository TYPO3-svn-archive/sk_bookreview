<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Steffen Kamper (steffen@dislabs.de)
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
 * Plugin 'Book Review' for the 'sk_bookreview' extension.
 *
 * @author	Steffen Kamper <steffen@dislabs.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('rtehtmlarea').'pi2/class.tx_rtehtmlarea_pi2.php'); //RTE  


class tx_skbookreview_pi1 extends tslib_pibase {
	/**
	 * Book Reviews
	 */
	 
	var $prefixId = 'tx_skbookreview_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_skbookreview_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'sk_bookreview';	// The extension key.
	var $pi_checkCHash = TRUE;
	
	var $conf;
	var $config;
	var $page_id;
	var $content;
	var $RecordsComplete;
	
	var $authorpage="";
	var $pidlist;
	var $savePID;
    
    var $uploadPath = 'uploads/tx_skbookreview/';
    
	/* RTE vars */
	var $RTEObj;
    var $strEntryField;
    var $docLarge = 0;
    var $RTEcounter = 0;
    var $formName;
    var $additionalJS_initial = '';		// Initial JavaScript to be printed before the form (should be in head, but cannot due to IE6 timing bug)
	var $additionalJS_pre = array();	// Additional JavaScript to be printed before the form
	var $additionalJS_post = array();	// Additional JavaScript to be printed after the form
	var $additionalJS_submit = array();	// Additional JavaScript to be executed on submit
    var $PA = array(
            'itemFormElName' =>  '',
            'itemFormElValue' => '',
            );
    var $specConf = array();
    var $thisConfig = array();
    var $RTEtypeVal = 'text';
    var $thePidValue;
    
    
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		// parse XML data into php array
		$this->pi_initPIflexForm(); 
		
		// get the values
		$this->config['view'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'view_input', 'sVIEW');
		$this->config['sort'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sort_input', 'sTEAS');
		$this->config['orderBy'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'view_Orderby', 'sTEAS');
		$this->config['groupcat'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'groupcat', 'sTEAS');
		$this->config['categorySelect'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'categorySelect', 'sTEAS');
		$this->config['categoryMode'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'categoryMode', 'sTEAS');
		$this->config['pageforsingleview'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pageforsingleview', 'sTEAS');
		$this->config['maxCoverwidth'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxCoverwidth', 'sLAYOUT');
		$this->config['maxCoverheight'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxCoverheight', 'sLAYOUT');
		$this->config['maxReviewOnPage'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxReviewOnPage', 'sLAYOUT');
		$this->config['altLayout'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'altLayout', 'sLAYOUT');
		$this->config['pageAll'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pageAll', 'sLAYOUT');
		$this->config['captionAltLayout'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'captionAltLayout', 'sLAYOUT');
		$this->config['pageBrowser'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pageBrowser', 'sLAYOUT');
		
        if(intval($this->config['maxCoverwidth'])==0) $this->config['maxCoverwidth']=$this->conf['maxCoverWidth'];
        if(intval($this->config['maxCoverheight'])==0) $this->config['maxCoverheight']=$this->conf['maxCoverHeight'];
        
		$this->page_id = $GLOBALS["TSFE"]->id;
		$this->pidList = $this->pi_getPidList($this->cObj->data['pages'],$this->conf["recursive"]);
		$this->savePID=$this->conf['FEeditSavePID'] ? $this->conf['FEeditSavePID']  :   $this->cObj->data['pages'];
        
        
		switch($this->config['view']) {
			case 'single':
				$this->content .= $this->displaySingle();
				break;
			case 'teaser':
				$this->content .= $this->displayList('teaser');
				break;
			case 'list' :
				$this->content .= $this->displayList('list');
				break;	
			case 'menu' :
				$this->content .= $this->displayMenu();
				break;	
            case 'FEedit' :
                $this->content .= $this->displayFEForm();
		}
		
		
		
		return $this->pi_wrapInBaseClass($this->content);
		
	}
	
	function displaySingle() {
		
		$content='';
		$forumid=0; //only needed for TP
		
		$singleWhere = 'tx_skbookreview_books.uid=' . intval($this->piVars['bookreview']);
		
		$selectConf['selectFields']='tx_skbookreview_books.*,tx_skbookreview_category.category as catname';
		$selectConf['groupBy'] = 'tx_skbookreview_books.uid';
		$selectConf['orderBy']='';
		$selectConf['leftjoin']='tx_skbookreview_books_category_mm on tx_skbookreview_books_category_mm.uid_local = tx_skbookreview_books.uid LEFT OUTER JOIN tx_skbookreview_category on tx_skbookreview_category.uid= tx_skbookreview_books_category_mm.uid_foreign';
		$selectConf['where']=$singleWhere;
		$selectConf['pidInList']=$this->pidList;
		$res=$this->cObj->exec_getQuery('tx_skbookreview_books', $selectConf);
		
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		for($i=0;$i<4;$i++) {
			$level_label[$i]=$this->pi_getLL('skbookreview_label.level.I.'.$i);
		}
		
		if (is_array($row)) { 
		// a record exists
			
			// set the title of the single view page to the title of the review record
			if ($this->conf['substitutePagetitle']) {
				$GLOBALS['TSFE']->page['title'] = $row['title'];
				// set pagetitle for indexed search to news title
				$GLOBALS['TSFE']->indexedDocTitle = $row['title'];
			}
			
			//Links zu wrappen ?
			$wrappedSubpartArray=Array();
			
			//Images vorbereiten
			$img['file'] = 'uploads/tx_skbookreview/'. $row["cover"];
            if($this->config['maxCoverwidth']>0) $img['file.']['maxW']=$this->config['maxCoverwidth'];
            if($this->config['maxCoverheight']>0) $img['file.']['maxH']=$this->config['maxCoverheight'];
			$img['params'] = $this->pi_classParam('image');
			$img['altText'] = 'Coverimage';
			$img['titleText'] = $row['title'];
			$coverImgCode = $this->cObj->IMAGE($img);
			//Pointsimage
			$img["file"] = 'typo3conf/ext/sk_bookreview/'. $row["points"].'.gif';
			$img['params'] = $this->pi_classParam('image');
			$img['altText'] = $row["points"].' '.$this->pi_getLL('skbookreview_label.points');
			$img['titleText'] = $this->pi_getLL('skbookreview_label.points');
			$pointsImgCode = $this->cObj->IMAGE($img);
			
			//Links vorbereiten
			$lnk['extTarget']="_blank";
			$lnk['parameter']=$row['buylink'];
			$lnktext=(substr($row['buylink'],0,7)=="http://") ? substr($row['buylink'],7) : $row['buylink'];
			$lnktext=substr($lnktext,0,strpos($lnktext,"/"));
			$theLinkCode=$this->cObj->typoLink($lnktext,$lnk); 
			
			$lnk['extTarget']="_blank";
			$lnk['parameter']=$row['link'];
			$lnktext=(substr($row['link'],0,7)=="http://") ? substr($row['link'],7) : $row['link'];
			$lnktext=substr($lnktext,0,strpos($lnktext,"/"));
			$theLinkCode2=$this->cObj->typoLink($lnktext,$lnk); 
			
			//ist in der Adressliste ?
			//$forumid=$this->IsInAdresslist(strtr($row['reviewer'],array("("=>"",")"=>"")));
			$pivarsArr=array(
				$this->prefixId.'[bookreview]' =>$row['uid'],
				$this->prefixId.'[backpid]'=>$this->page_id,
				$this->prefixId.'[bookcat]'=>$this->piVars['bookcat'],
				$this->prefixId.'[page]'=>$this->piVars['page'],
			);
				
			//load template
			$template=$this->cObj->fileResource($this->conf['templateFile']);
			//fil markers
			$this->labelMarkers($markerArray);
			$markerArray['###TITLE###'] = $row['title'];
			$markerArray["###CATEGORY###"] =$row['catname'];
			$markerArray['###COVERIMAGE###'] = $coverImgCode;
			$markerArray['###AUTHOR###'] = $row['author']; 
			$markerArray['###PUBLISHER###'] = $row['publisher']; 
			$markerArray['###ADDITIONAL###'] = $row['additional']; 
			$markerArray['###LEVEL###'] = $level_label[$row['level']]; 
			$markerArray['###POINTS###'] = $pointsImgCode;
			$markerArray['###LINK###'] = ($row['link']=='') ? '' : $theLinkCode2;
			$markerArray['###IMPRESSION###'] = nl2br($row['impression']); 
			$markerArray['###DESCRIPTION###'] = $this->pi_RTEcssText($row['description']);
			$markerArray['###RESULT###'] = nl2br($row['result']); 
			$markerArray['###PAGES###'] = $row['pages']; 
			$markerArray['###ISBN###'] = $row['isbn']; 
			$markerArray['###PRICE###'] = $row['price'];
			$markerArray['###BUYLINK###'] = $theLinkCode;
			$markerArray['###DATEOFREVIEW###'] = date("d.m.Y",$row['date']);
			$markerArray['###REVIEWER###'] = $row['reviewer'];
			$markerArray['###BACKLINK###']=$this->cObj->stdWrap($this->pi_linkTP($this->pi_getLL('back'),$pivarsArr,1,$this->piVars['backpid']),$this->conf['backLink_stdWrap.']);
			
			$content = $this->cObj->substituteMarkerArray($subpart, $markerArray);
			$subpart=$this->cObj->getSubpart($template,"###TEMPLATE_SINGLE###");
			
			$content = $this->cObj->substituteMarkerArray($subpart, $markerArray);
			
			
		} else {
			$noReviewIdMsg = $this->cObj->stdWrap($this->pi_getLL('no_index'), '<p>|</p>');
			$content = $noReviewIdMsg;
		}
		return $content;
	}
	
	function typicalMarkers(&$markerArray,$row) {
	
	}
	
	function labelMarkers(&$markerArray) {
		$markerArray["###CATEGORY_LABEL###"] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.category'),$this->conf['labelWrap']);
		$markerArray['###AUTHOR_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.author'),$this->conf['labelWrap']); 
		$markerArray['###PUBLISHER_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.publisher'),$this->conf['labelWrap']); 
		$markerArray['###ADDITIONAL_LABEL###'] =$this->cObj->wrap($this->pi_getLL('skbookreview_label.additional'),$this->conf['labelWrap']); 
		$markerArray['###POINTS_LABEL###'] =$this->cObj->wrap( $this->pi_getLL('skbookreview_label.points'),$this->conf['labelWrap']);
		$markerArray['###LEVEL_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.level'),$this->conf['labelWrap']); 
		$markerArray['###LINK_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.link'),$this->conf['labelWrap']);
		$markerArray['###IMPRESSION_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.impression'),$this->conf['labelWrap']); 
		$markerArray['###DESCRIPTION_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.description'),$this->conf['labelWrap']);
		$markerArray['###RESULT_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.result'),$this->conf['labelWrap']); 
		$markerArray['###PAGES_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.pages'),$this->conf['labelWrap']); 
		$markerArray['###ISBN_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.isbn'),$this->conf['labelWrap']); 
		$markerArray['###PRICE_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.price'),$this->conf['labelWrap']);
		$markerArray['###BUYLINK_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.buylink'),$this->conf['labelWrap']);
		$markerArray['###DATEOFREVIEW_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.date'),$this->conf['labelWrap']);
		$markerArray['###REVIEWER_LABEL###'] = $this->cObj->wrap($this->pi_getLL('skbookreview_label.reviewer'),$this->conf['labelWrap']);
	}
	
	function displayList($kind='list') {
	
		$reviews=$this->GetReviewData();
		$items=array();
		$template=$this->cObj->fileResource($this->conf['templateFile']);
		$temp_cat='';
		$count=0;
        #debug($this->config);
        
        $this->labelMarkers(&$markerArray);
		if(is_array($reviews)) {
			
            
			foreach ($reviews as $row) {
				$count++;
				
				$markerArray["###CATEGORYGROUP###"]='';
				if($this->config['groupcat']==1 && $temp_cat!=$row['catname']) {
					$markerArray["###CATEGORYGROUP###"] = $this->cObj->stdWrap($row['catname'],$this->conf['categoryGroup_stdWrap.']); 
                    $temp_cat=$row['catname'];
				}
				
				$img["file"] = 'uploads/tx_skbookreview/'. $row["cover"];
				$img['params'] = $this->pi_classParam('image');
				$img['altText'] = 'Coverimage';
				$img['titleText'] = $row['title'];
				if($this->config['maxCoverwidth']>0) $img['file.']['maxW']=$this->config['maxCoverwidth'];
				if($this->config['maxCoverheight']>0) $img['file.']['maxH']=$this->config['maxCoverheight'];
				$tempItem['image'] = $this->cObj->IMAGE($img);
				$tempItem['link_id'] = $GLOBALS['TSFE']->sys_language_uid != 0 ? $row[$this->lang_prefix."pid"] : $row["uid"];
				
				$pivarsArr=array(
					'bookreview' =>$row['uid'],
					'backpid'=>$this->page_id,
					'bookcat'=>$this->piVars['bookcat'],
					'page'=>$this->piVars['page']
				);
				
				
				$markerArray["###COVERIMAGE###"] =$tempItem['image'];
				$markerArray["###CATEGORY###"] = $row['catname'];
				$markerArray["###TITLE###"] = $this->cObj->stdWrap($row['title'],$this->conf['title_stdWrap.']);
				$markerArray['###AUTHOR###'] = $row['author']; 
				$markerArray['###PUBLISHER###'] = $row['publisher']; 
				$markerArray['###ADDITIONAL###'] = $row['additional']; 
				$markerArray['###LEVEL###'] = $this->pi_getLL('skbookreview_label.level').'</p>'.$row['level']; 
				$markerArray['###POINTS###'] = $this->pi_getLL('skbookreview_label.points').'</p>'.$row['points'];
				$markerArray['###LINK###'] = $this->pi_getLL('skbookreview_label.link').'</p>'.$row['link'];
				$markerArray['###IMPRESSION###'] = nl2br($row['impression']); 
				$markerArray['###DESCRIPTION###'] = nl2br($row['description']);
				$markerArray['###RESULT###'] = nl2br($row['result']); 
				$markerArray['###PAGES###'] = $row['pages']; 
				$markerArray['###ISBN###'] = $row['isbn']; 
				$markerArray['###PRICE###'] = $row['price'];
				$markerArray['###BUYLINK###'] = $theLinkCode;
				$markerArray['###DATEOFREVIEW###'] = date("d.m.Y",$row['date']);
				$markerArray['###REVIEWER###'] = $row['reviewer'];
				$markerArray["###LINK_ITEM###"] = $link;
				
                $wrappedSubpartArray['###LINK_ITEM_WRAP###'] = explode('|', $this->pi_linkTP_keepPIvars('|', $pivarsArr, 1, 1,$this->config['pageforsingleview']));
                
				if(($this->config['altLayout']>0 && $this->config['altLayout']<count($reviews) && $count>=$this->config['altLayout']) || $this->config['altLayout']==1 ) {
                    $template_books= $kind=='list' ? $this->cObj->getSubpart($template,"###TEMPLATE_LIST_ALT###") : $this->cObj->getSubpart($template,"###TEMPLATE_TEASER_ALT###");
				} else {
					$template_books= $kind=='list' ? $this->cObj->getSubpart($template,"###TEMPLATE_LIST###") : $this->cObj->getSubpart($template,"###TEMPLATE_TEASER###");
				}
                $subpart=$this->cObj->getSubpart($template_books,"###BOOKS###"); 	
				
                $replaced.=$this->cObj->substituteMarkerArrayCached($subpart, $markerArray,$subpartArray,$wrappedSubpartArray);
				
				if($this->config['altLayout']>0 && $this->config['altLayout']<count($reviews) && $count==$this->config['altLayout'] && $this->config['captionAltLayout']!='')
					$replaced.=$this->config['captionAltLayout'];
				
				 
			}
            $subpartArray['###BOOKS###']=$replaced;
            #$markerArray=array();
            //PAGEBROWSER ?
			$markerArray["###PAGEBROWSER###"]='';
			if($this->config['pageBrowser']==1 && $this->config['maxReviewOnPage']<$this->RecordsComplete) {
				$markerArray["###PAGEBROWSER###"]='<div class="css-bookreview-pagebrowser">'.$this->pi_getLL('page');
				for ($i = 0 ; $i < ($this->RecordsComplete/$this->config["maxReviewOnPage"]); $i++) 	{
					if ($this->piVars['page']==$i) 	{
						$markerArray["###PAGEBROWSER###"].= '<span class="css-bookreview-pagebrowser-pages">'.(string)($i+1).'</span>';
					} else {
						$markerArray["###PAGEBROWSER###"].= '<span class="css-bookreview-pagebrowser-pages">'.$this->pi_linkTP((string)($i+1),array($this->prefixId.'[page]'=>(string)($i),$this->prefixId.'[bookcat]'=>$this->piVars['bookcat'])).'</span>';
					}
				}
				$markerArray["###PAGEBROWSER###"].= '</div>';
			}
            $content.=$this->cObj->substituteMarkerArrayCached($template_books, $markerArray,$subpartArray,array());
		} else {
			$noRecordsMsg = $this->cObj->stdWrap($this->pi_getLL('no_records'), $this->conf['noRecords_stdWrap.']);
			$content = $noRecordsMsg;
		}
		if($this->config['pageAll']!='') $content.='<div class="css-bookreview-morelink">'.$this->cObj->getTypoLink($this->pi_getLL('more_reviews'),$this->config['pageAll'],array()).'</div>';
		return $content;
	}
	function DisplayMenu() {
		$m='<ul>';
		if($this->config['pageforsingleview']=='') {
			return "Please select a page for the Singleview as Target !";
		} else {
			$where_clause='';
			if($this->config['categoryMode']==1) {
				if(strpos($this->config['categorySelect'],',')>0) {
					$where_clause="where tx_skbookreview_category.uid in (".$this->config['categorySelect'].")";
				} else {
					$where_clause="where tx_skbookreview_category.uid =".$this->config['categorySelect'];
				}
			} elseif($this->config['categoryMode']==2) {
				if(strpos($this->config['categorySelect'],',')>0) {
					$where_clause="where tx_skbookreview_category.uid not in (".$this->config['categorySelect'].")";
				} else {
					$where_clause="where tx_skbookreview_category.uid !=".$this->config['categorySelect'];
				}
			}
			
			$sql="select tx_skbookreview_category.*,count(tx_skbookreview_books_category_mm.uid_foreign)as anz from tx_skbookreview_category
LEFT JOIN tx_skbookreview_books_category_mm ON tx_skbookreview_books_category_mm.uid_foreign=tx_skbookreview_category.uid
GROUP BY tx_skbookreview_category.uid $where_clause 
	ORDER BY  tx_skbookreview_category.category ";
			$res=$GLOBALS['TYPO3_DB']->sql_query($sql);
			if(mysql_num_rows($res)>0) {
                $anz=0;
				while($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$m.= '<li>'.$this->pi_linkTP($temp['category'].' ('.$temp['anz'].')',array($this->prefixId.'[bookcat]' => $temp['uid']),1,$this->config['pageforsingleview']).'</li>';
                    $anz+=$temp['anz'];
				}
			}
			$m.= '<li>'.$this->pi_linkTP($this->pi_getLL('skbookreview_allbooks').' ('.$anz.')',array(),1,$this->config['pageforsingleview']).'</li>';
			$m.='</ul>';
			return $this->cObj->stdWrap($m,$this->conf['catMenu_stdWrap.']);
		}
	}
    
    function displayFEForm() { 
        $content='';
        #t3lib_div::debug($this->conf);
        
        #debug($this->piVars);
        #debug($_FILES);
        $markerArray['###ACTION###'] = ''; 
        
        $err=''; 
        if(isset($this->piVars['submit'])) {
            //validate
            
            if($this->piVars['title']=='')  $err.=  $this->pi_getLL('validate.notitle').'<br />';
            if(!is_array($this->piVars['category'])) $err.= $this->pi_getLL('validate.nocategory').'<br />';
            
            
            if($_FILES[$this->prefixId]['name']['cover']!='') {
				$uploadfile1 = $_FILES[$this->prefixId]['name']['cover'];
				if(is_file($this->uploadPath.$uploadfile1)) $uploadfile1=$this->uniqueFilename($this->uploadPath,$uploadfile1);
				if(move_uploaded_file($_FILES[$this->prefixId]['tmp_name']['cover'], $this->uploadPath.$uploadfile1)) {
					@chmod($this->uploadPath.$uploadfile1,0755);
					$this->piVars['cover_uploaded']=$uploadfile1;
				}
			}
            
        }
        
        if(!isset($this->piVars['submit']) || (isset($this->piVars['submit']) && $err!='') ) {
            $template=$this->cObj->fileResource($this->conf['templateFile']); 
            $subpart=$this->cObj->getSubpart($template,"###TEMPLATE_FE_EDIT###");
            #debug($subpart);
            
            $markerArray['###ACTION###'] = '';
            $markerArray['###HIDDEN###'] .= '<input type="hidden" name="no_cache" value="1" />'; 
            if(isset($this->piVars['cover_uploaded'])) $markerArray['###HIDDEN###'] .= '<input type="hidden" name="'.$this->prefixId.'[cover_uploaded]" value="'.$this->piVars['cover_uploaded'].'">';
            $markerArray['###ERROR###'] = $err=='' ? '' : $this->cObj->stdWrap($err,$this->conf['error_stdWrap.']);   
            
            //LABELS
            $markerArray['###LEGEND_BOOK###'] = $this->pi_getLL('legend.books');   
            $markerArray['###LEGEND_BOOKTEXTS###'] = $this->pi_getLL('legend.booktexts');     
            $markerArray['###LEGEND_BOOKREVIEW###'] = $this->pi_getLL('legend.bookreview');     
            
            $markerArray['###L_TITLE###'] = $this->pi_getLL('skbookreview_label.title');  
            $markerArray['###L_CATEGORY###'] = $this->pi_getLL('skbookreview_label.category');    
            $markerArray['###L_COVER###'] = $this->pi_getLL('skbookreview_label.cover');     
            $markerArray['###L_AUTHOR###'] = $this->pi_getLL('skbookreview_label.author');    
            $markerArray['###L_PUBLISHER###'] = $this->pi_getLL('skbookreview_label.publisher');     
            $markerArray['###L_ADDITIONAL###'] = $this->pi_getLL('skbookreview_label.additional');     
            $markerArray['###L_LEVEL###'] = $this->pi_getLL('skbookreview_label.level');    
            $markerArray['###L_LINK###'] = $this->pi_getLL('skbookreview_label.link');    
            $markerArray['###L_POINTS###'] = $this->pi_getLL('skbookreview_label.points');    
            $markerArray['###L_IMPRESSION###'] = $this->pi_getLL('skbookreview_label.impression');     
            $markerArray['###L_DESCRIPTION###'] = $this->pi_getLL('skbookreview_label.description');     
            $markerArray['###L_RESULT###'] = $this->pi_getLL('skbookreview_label.result');     
            $markerArray['###L_CONTENT###'] = $this->pi_getLL('skbookreview_label.pages');   
            $markerArray['###L_PRICE###'] = $this->pi_getLL('skbookreview_label.price');   
            $markerArray['###L_ISBN###'] = $this->pi_getLL('skbookreview_label.isbn');   
            $markerArray['###L_BUYLINK###'] = $this->pi_getLL('skbookreview_label.buylink');    
            $markerArray['###L_REVIEWER###'] = $this->pi_getLL('skbookreview_label.reviewer');    
            
            $markerArray['###SUBMIT_VALUE###'] = $this->pi_getLL('feform.submit');  
            
            //FIELDS
            $markerArray['###TITLE###'] = $this->prefixId.'[title]'; 
            $markerArray['###CATEGORY###'] = $this->prefixId.'[category][]'; 
            $markerArray['###COVER###'] = $this->prefixId.'[cover]'; 
            $markerArray['###AUTHOR###'] = $this->prefixId.'[author]'; 
            $markerArray['###PUBLISHER###'] = $this->prefixId.'[publisher]'; 
            $markerArray['###ADDITIONAL###'] = $this->prefixId.'[additional]'; 
            $markerArray['###LEVEL###'] = $this->prefixId.'[level]'; 
            $markerArray['###LINK###'] = $this->prefixId.'[link]'; 
            $markerArray['###POINTS###'] = $this->prefixId.'[points]'; 
            $markerArray['###CONTENT###'] = $this->prefixId.'[content]'; 
            $markerArray['###PRICE###'] = $this->prefixId.'[price]'; 
            $markerArray['###ISBN###'] = $this->prefixId.'[isbn]'; 
            $markerArray['###BUYLINK###'] = $this->prefixId.'[buylink]'; 
            $markerArray['###REVIEWER###'] = $this->prefixId.'[reviewer]'; 
            $markerArray['###SUBMIT###'] = $this->prefixId.'[submit]'; 
            
            //VALUES
            $markerArray['###TITLE_VALUE###'] = $this->piVars['title'];  
            $markerArray['###COVER_VALUE###'] =  '';  
            if(isset($this->piVars['cover_uploaded'])) {
               $img['file']=$this->uploadPath.$this->piVars['cover_uploaded'];
               $img['params'] = $this->pi_classParam('image');
			   $img['altText'] = 'Coverimage';
			   $img['titleText'] = $row['title'];
			   if($this->conf['maxCoverwidth']>0) $img['file.']['maxW']=$this->conf['maxCoverwidth'];
			   if($this->conf['maxCoverheight']>0) $img['file.']['maxH']=$this->conf['maxCoverheight'];
               debug($img);
               $markerArray['###COVER_VALUE###'] = $this->cObj->IMAGE($img);
            }    
            $markerArray['###AUTHOR_VALUE###'] = $this->piVars['author'];  
            $markerArray['###PUBLISHER_VALUE###'] = $this->piVars['publisher'];  
            $markerArray['###ADDITIONAL_VALUE###'] = $this->piVars['additional'];  
            $markerArray['###LINK_VALUE###'] = $this->piVars['link'];  
            $markerArray['###POINTS_VALUE###'] = $this->piVars['points'];  
            $markerArray['###CONTENT_VALUE###'] = $this->piVars['content'];  
            $markerArray['###PRICE_VALUE###'] = $this->piVars['price'];  
            $markerArray['###ISBN_VALUE###'] = $this->piVars['isbn'];  
            $markerArray['###BUYLINK_VALUE###'] = $this->piVars['buylink'];  
            $markerArray['###REVIEWER_VALUE###'] = $this->piVars['reviewer'];  
            
            
            $markerArray['###CATEGORY_LIST###'] = $this->getCategorieOptions($this->piVars['category']);  
            $markerArray['###LEVEL_VALUE###'] = '
            <option value="0" '.($this->piVars['level']=="0" ? 'selected="selected"' : '').'>'.$this->pi_getLL('skbookreview_label.level.I.0').'</option>
            <option value="1" '.($this->piVars['level']=="1" ? 'selected="selected"' : '').'>'.$this->pi_getLL('skbookreview_label.level.I.1').'</option>
            <option value="2" '.($this->piVars['level']=="2" ? 'selected="selected"' : '').'>'.$this->pi_getLL('skbookreview_label.level.I.2').'</option>
            <option value="3" '.($this->piVars['level']=="3" ? 'selected="selected"' : '').'>'.$this->pi_getLL('skbookreview_label.level.I.3').'</option>
            ';  
           
            $markerArray['###POINTS_VALUE###'] = '
            <option value="1" '.($this->piVars['points']=="1" ? 'selected="selected"' : '').'>1</option>
            <option value="2" '.($this->piVars['points']=="2" ? 'selected="selected"' : '').'>2</option>
            <option value="3" '.($this->piVars['points']=="3" ? 'selected="selected"' : '').'>3</option>
            <option value="4" '.($this->piVars['points']=="4" ? 'selected="selected"' : '').'>4</option>
            <option value="5" '.($this->piVars['points']=="5" ? 'selected="selected"' : '').'>5</option>
            ';  
            
            /* Start setting the RTE markers */
		    if(!$this->RTEObj)  $this->RTEObj = t3lib_div::makeInstance('tx_rtehtmlarea_pi2');
		    if($this->RTEObj->isAvailable()) {
			    $this->RTEcounter++;
			    $this->formName = 'tx_skbookreview_form';
			    $this->strEntryField = 'impression';
			    $this->PA['itemFormElName'] = $this->prefixId.'[impression]';
			    $this->PA['itemFormElValue'] = $this->piVars['impression'];
			    $this->thePidValue = $GLOBALS['TSFE']->id;
			    $RTEItem = $this->RTEObj->drawRTE($this,'tx_skbookreview_books',$this->strEntryField,$row=array(), $this->PA, $this->specConf, $this->thisConfig, $this->RTEtypeVal, '', $this->savePID);
			    $markerArray['###IMPRESSION###'] = $RTEItem; 
                
                $this->RTEcounter++;
			    $this->formName = 'tx_skbookreview_form';
			    $this->strEntryField = 'description';
			    $this->PA['itemFormElName'] = $this->prefixId.'[description]';
			    $this->PA['itemFormElValue'] = $this->piVars['description'];
			    $this->thePidValue = $GLOBALS['TSFE']->id;
			    $RTEItem = $this->RTEObj->drawRTE($this,'tx_skbookreview_books',$this->strEntryField,$row=array(), $this->PA, $this->specConf, $this->thisConfig, $this->RTEtypeVal, '', $this->savePID);
			    $markerArray['###DESCRIPTION###'] = $RTEItem;
                
                $this->RTEcounter++;
			    $this->formName = 'tx_skbookreview_form';
			    $this->strEntryField = 'result';
			    $this->PA['itemFormElName'] = $this->prefixId.'[result]';
			    $this->PA['itemFormElValue'] = $this->piVars['result'];
			    $this->thePidValue = $GLOBALS['TSFE']->id;
			    $RTEItem = $this->RTEObj->drawRTE($this,'tx_skbookreview_books',$this->strEntryField,$row=array(), $this->PA, $this->specConf, $this->thisConfig, $this->RTEtypeVal, '', $this->savePID);
			    $markerArray['###RESULT###'] = $RTEItem;
                
                $markerArray['###ADDITIONALJS_PRE###'] = $this->additionalJS_initial.'
				    <script type="text/javascript">'. implode(chr(10), $this->additionalJS_pre).'
				    </script>';
			    $markerArray['###ADDITIONALJS_POST###'] = '
				    <script type="text/javascript">'. implode(chr(10), $this->additionalJS_post).'
				    </script>';
			    $markerArray['###ADDITIONALJS_SUBMIT###'] = implode(';', $this->additionalJS_submit);
			    
		    }
		    /* End setting the RTE markers */
                    
                    
            $content.=$this->cObj->substituteMarkerArray($subpart, $markerArray); 
        }
        
        if(isset($this->piVars['submit']) && $err=='') {
            $insertArr = array(
               'crdate' => time(),
               'tstamp' => time(),
               'pid' => $this->savePID,
               'hidden' => '1',
               'title' => $this->piVars['title'],
               'cover' => $this->piVars['cover_uploaded'],
               'author' => $this->piVars['author'],
               'publisher' => $this->piVars['publisher'],
               'level' => $this->piVars['level'],
               'link' => $this->piVars['link'],
               'points' => $this->piVars['points'],
               'impression' => $this->piVars['impression'],
               'description' => $this->piVars['description'],
               'result' => $this->piVars['result'],
               'pages' => $this->piVars['pages'],
               'price' => $this->piVars['price'],
               'isbn' => $this->piVars['isbn'],
               'date' => time(),
               'buylink' => $this->piVars['buylink'],
               'reviewer' => $this->piVars['reviewer'],
               'additional' => $this->piVars['additional'],
               'category' => count($this->piVars['category']),
            );
            
            $res=$GLOBALS['TYPO3_DB']->exec_INSERTquery(
              'tx_skbookreview_books',
              $insertArr
            );
            $uid=mysql_insert_id();
            $i=1;
            
            foreach($this->piVars['category'] as $c) {
                $res=$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_skbookreview_books_category_mm',array(
                     'uid_local' => $uid,
                     'uid_foreign' => $c,
                     'sorting' => $i++,
                ));
                     
            }
            $content = $this->cObj->stdWrap($this->pi_getLL('feform.entrycreated'),$this->conf['entryCreated_stdWrap.']);    
        }
        return $content;    
    }
    
    function getCategorieOptions($val_arr) {
        if(!is_array($val_arr)) $val_arr=array();
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,category', 'tx_skbookreview_category', 'hidden=0 and deleted=0 and pid IN ('.$this->pidList.')');
        if($res) {
            while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { 
              $CO.='<option value="'.$row['uid'].'" '.(in_array($row['uid'],$val_arr) ? 'selected="selected"' : '').'>'.$row['category'].'</option>';
            }   
        }
        return $CO;
    }
    
	function GetReviewData() {
		//Build Query
		
		$data=array();
		$where_clause='';
		if($this->piVars[bookcat]>0) {
			$where_clause="and tx_skbookreview_category.uid =".$this->piVars[bookcat];
		} else {
			if($this->config['categoryMode']==1) {
				if(strpos($this->config['categorySelect'],',')>0) {
					$where_clause="and tx_skbookreview_category.uid in (".$this->config['categorySelect'].")";
				} else {
					$where_clause="and tx_skbookreview_category.uid =".$this->config['categorySelect'];
				}
			} elseif($this->config['categoryMode']==2) {
				if(strpos($this->config['categorySelect'],',')>0) {
					$where_clause="and tx_skbookreview_category.uid not in (".$this->config['categorySelect'].")";
				} else {
					$where_clause="and tx_skbookreview_category.uid !=".$this->config['categorySelect'];
				}
			}
		}
		
		if($this->pidList!="") $where_clause.=" and tx_skbookreview_books.pid in(".$this->pidList.")";
		
		$orderBy=$this->config['groupcat']==0 ? '' : 'catname asc,';
		if($this->config['orderBy']=='datetime') $orderBy.='date';
		elseif($this->config['orderBy']=='reviewer') $orderBy.='reviewer';
		elseif($this->config['orderBy']=='author') $orderBy.='author';
		elseif($this->config['orderBy']=='title') $orderBy.='title';
		
		if($this->config['sort']=='asc') $orderBy.=' asc';
		if($this->config['sort']=='desc') $orderBy.=' desc';
		if($this->config['sort']=='random') $orderBy='RAND()';
		
		if(intval($this->config['maxReviewOnPage'])>0)
			$limit=intval($this->config['maxReviewOnPage']);
		
		/*
		$selectConf['selectFields']='COUNT(DISTINCT(tx_skbookreview_books.uid))';
		$selectConf['leftjoin']='tx_skbookreview_books_category_mm on tx_skbookreview_books_category_mm.uid_local = tx_skbookreview_books.uid';
		$selectConf['where']=$where_clause;
		$selectConf['pidInList']=$this->pidList;
		
        $res=$this->cObj->exec_getQuery('tx_skbookreview_books', $selectConf);
		*/
        
        $res=$GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
            'COUNT(*)',
            'tx_skbookreview_books',
            'tx_skbookreview_books_category_mm',
            'tx_skbookreview_category',
            $where_clause,
            '',
           '' ,
            ''
            );
        
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		$this->RecordsComplete=$row[0];
		
		if($this->RecordsComplete>0) {
			$limit=($this->piVars['page']>0) ? $this->piVars['page']*$this->config['maxReviewOnPage'] : 0;
			
			$selectConf['orderBy']=$orderBy;
			$selectConf['selectFields']='tx_skbookreview_books.*,tx_skbookreview_category.category as catname';
			$selectConf['groupBy'] = 'tx_skbookreview_books.uid';
			$selectConf['leftjoin']='tx_skbookreview_books_category_mm on tx_skbookreview_books_category_mm.uid_local = tx_skbookreview_books.uid LEFT OUTER JOIN tx_skbookreview_category on tx_skbookreview_category.uid= tx_skbookreview_books_category_mm.uid_foreign';
			if($this->config['maxReviewOnPage']>0) {
				$selectConf['begin']=$limit;
				$selectConf['max']=$this->config['maxReviewOnPage'];
			}
			
			
			/*
			$sql="SELECT tx_skbookreview_books.* , tx_skbookreview_category.category AS catname
	FROM tx_skbookreview_books
	INNER JOIN tx_skbookreview_category ON tx_skbookreview_category.uid = tx_skbookreview_books.category $where_clause 
	ORDER BY $orderBy ".(($this->config['maxReviewOnPage']>0) ? "LIMIT $limit , ".$this->config['maxReviewOnPage'] : "");
	*/
    
            $res=$GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
            'tx_skbookreview_books.*,tx_skbookreview_category.category as catname',
            'tx_skbookreview_books',
            'tx_skbookreview_books_category_mm',
            'tx_skbookreview_category',
            $where_clause,
            '',
            $orderBy ,
            $this->config['maxReviewOnPage']>0 ? "$limit , ".$this->config['maxReviewOnPage'] : ""
            );
			
            while($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$data[] = $temp;
			}
		}
		return $data;
	}
	
	
	
	function getReviewSubpart($myTemplate, $myKey, $row = Array()) {
		return ($this->cObj->getSubpart($myTemplate, $myKey));
	}
	
	function IsInAdresslist($sn) {
		$sql="select forumid from tt_address where shortname='$sn'";
		$res=$GLOBALS['TYPO3_DB']->sql_query($sql);
		if(mysql_num_rows($res)>0) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			return $row[0];
		} else return 0;
	}
    function uniqueFilename($path,$f) {
		$i=1;
		$f=strtolower($f);
		$ersetzen = array( 
'ä' => 'ae', 
'ö' => 'oe', 
'ü' => 'ue', 
'ß' => 'ss',
' ' => '_',
'+' => '_',
'-' => '_',
'(' => '_',
')' => '_',
 
); 

		$f=strtr($f,$ersetzen);
		$ext=substr($f,strrpos($f,".")+1);
		$file=substr($f,0,strlen($f)-strlen($ext)-1);
		if(is_file($path.$file.'.'.$ext)) {
			while(is_file($path.$file.'_'.$i.'.'.$ext)) {$i++;}
			return $file.'_'.$i.'.'.$ext;
		} else return $file.'.'.$ext;
	}
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_bookreview/pi1/class.tx_skbookreview_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_bookreview/pi1/class.tx_skbookreview_pi1.php']);
}

?>
