<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Проекты");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	"projects", 
	array(
		"IBLOCK_TYPE" => "marsd_minicorp_s1",
		"IBLOCK_ID" => "17",
		"NEWS_COUNT" => "20",
		"USE_SEARCH" => "N",
		"USE_RSS" => "N",
		"NUM_NEWS" => "3",
		"NUM_DAYS" => "30",
		"YANDEX" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "N",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/projects/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "N",
		"AJAX_OPTION_JUMP" => "Y",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_PANEL" => "N",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"USE_PERMISSIONS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "PREVIEW_PICTURE",
			3 => "DATE_ACTIVE_FROM",
			4 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "SHOW_FRONT_PAGE",
			1 => "BANNER_SIZE",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "NAME",
			1 => "DETAIL_TEXT",
			2 => "DETAIL_PICTURE",
			3 => "DATE_ACTIVE_FROM",
			4 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "SHOW_FRONT_PAGE",
			1 => "BANNER_SIZE",
			2 => "FORM_ORDER",
			3 => "DOCUMENTS_TITLE",
			4 => "GALLERY_TITLE",
			5 => "LINK_FAQ",
			6 => "LINK_REVIEWS",
			7 => "LINK_STAFF",
			8 => "LINK_PROJECTS",
			9 => "BANNER_VISIBLE",
			10 => "BANNER_BUTTON_TEXT",
			11 => "BANNER_TEXT_COLOR",
			12 => "BANNER_BG_COLOR",
			13 => "BANNER_TEXT",
			14 => "INFO_SITE",
			15 => "INFO_SCOPE",
			16 => "INFO_DATE",
			17 => "INFO_CLIENT",
			18 => "BANNER_TEXT_CODE",
			19 => "DOCUMENTS",
			20 => "MORE_PHOTO",
			21 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "Новости",
		"DETAIL_PAGER_TEMPLATE" => "modern",
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Проекты",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "modern",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => "projects",
		"SET_LAST_MODIFIED" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "Y",
		"SHOW_OTHER_NEWS" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"OTHER_ITEMS_TITLE" => "Другие новости",
		"PROJECTS_BLOCK_DESCRIPTION" => "Наша компания оказывает полный спектр услуг. У нашей компании прекрасная репутация, и мы уже успели завоевать доверие и расположение своих клиентов. Во многом это связано с широким спектром услуг, предоставляемых нашей компанией, а также высоким качеством обслуживания.",
		"PROJECTS_BLOCK_TITLE" => "Наши проекты",
		"PROJECTS_SHOW_DESCRIPTION" => "Y",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>