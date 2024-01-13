<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
Loader::includeModule("iblock");

$arComponentParameters = array(
	"PARAMETERS" => array(
		"PRODUCTS_IBLOCK_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
        "CLASSIFIRE_IBLOCK_ID" => array(
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CLASSIFIRE_IBLOCK_ID"),
            "TYPE" => "STRING",
        ),
        "TEMPLATE_DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
            "DETAIL",
            "DETAIL_URL",
            GetMessage("SIMPLECOMP_EXAM2_DETAIL_TEMPLATE_URL"),
            "",
            "URL_TEMPLATES"
        ),
        "CLASSIFIRE_PROPERTY_CODE" => array(
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CLASSIFIRE_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ),
        "CACHE_TIME"  =>  ["DEFAULT"=>36000000]
	),
);