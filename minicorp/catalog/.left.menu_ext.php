<? 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
global $APPLICATION; 
$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections", "", Array( 
   "ID"   =>   "", 
   "IBLOCK_TYPE"   =>   "marsd_minicorp_s1", 
   "IBLOCK_ID"   =>   "19", 
   "SECTION_URL"   =>   "/catalog/#SECTION_CODE_PATH#/", 
   "DEPTH_LEVEL"   =>   "3", 
   "CACHE_TYPE"   =>   "A", 
   "CACHE_TIME"   =>   "3600" 
   ) 
); 
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt); 
?>