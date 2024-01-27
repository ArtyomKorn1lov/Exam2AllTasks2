<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"PARAMETERS" => array(
		"NEWS_IBLOCK_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
        "PROPERTY_CODE_AUTHOR" => array(
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_CODE_AUTHOR"),
            "TYPE" => "STRING",
        ),
        "UF_CODE_AUTHOR" => array(
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_UF_CODE_AUTHOR"),
            "TYPE" => "STRING",
        ),
        "CACHE_TIME"  =>  ["DEFAULT"=>36000000]
	),
);