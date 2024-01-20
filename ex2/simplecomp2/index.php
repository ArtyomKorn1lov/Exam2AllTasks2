<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент 2");
?><?$APPLICATION->IncludeComponent(
	"ex2:simplecomp.exam2", 
	".default", 
	array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CLASSIFIRE_IBLOCK_ID" => "5",
		"CLASSIFIRE_PROPERTY_CODE" => "FIRM",
		"PRODUCTS_IBLOCK_ID" => "2",
		"TEMPLATE_DETAIL_URL" => "/catalog_exam/#SECTION_ID#/#ELEMENT_CODE#",
		"COMPONENT_TEMPLATE" => ".default",
		"DETAIL_URL" => "catalog_exam/#SECTION_ID#/#ELEMENT_CODE#"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>