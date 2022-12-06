<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION, $arSetting;

$aMenuLinksExt = array();

if($arSetting["USE_SERVICES_WITH_SECTION"]["VALUE"] == "N"){
    $aMenuLinksExt = $APPLICATION->IncludeComponent(
        "boxsol:menu.elements",
        "",
        array(
            "IBLOCK_TYPE" => "marsd_minicorp_s1",
            "IBLOCK_ID" => "18",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y",
        )
    );
} elseif($arSetting["USE_SERVICES_WITH_SECTION"]["VALUE"] == "Y"){
    $aMenuLinksExt = $APPLICATION->IncludeComponent(
        "boxsol:menu.sections",
        "",
        Array(
            "IS_SEF" => "Y",
            "SEF_BASE_URL" => "/services/",
            "SECTION_PAGE_URL" => "#SECTION_CODE#/",
            "DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_CODE#/",
            "IBLOCK_TYPE" => "marsd_minicorp_s1",
            "IBLOCK_ID" => "18",
            "DEPTH_LEVEL" => "2",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y",
        )
    );
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>
