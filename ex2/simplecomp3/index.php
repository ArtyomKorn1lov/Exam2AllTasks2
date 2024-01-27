<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент 3");
?><?$APPLICATION->IncludeComponent(
	"ex2:simplecomp.exam3",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"NEWS_IBLOCK_ID" => "1",
		"PROPERTY_CODE_AUTHOR" => "AUTHOR",
		"UF_CODE_AUTHOR" => "UF_AUTHOR_TYPE"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>