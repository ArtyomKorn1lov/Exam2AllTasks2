<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
{
	ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
	return;
}

if (!isset($arParams["PRODUCTS_IBLOCK_ID"]) || empty($arParams["PRODUCTS_IBLOCK_ID"])) {
    $arParams["PRODUCTS_IBLOCK_ID"] = 2;
}
if (!isset($arParams["CLASSIFIRE_IBLOCK_ID"]) || empty($arParams["CLASSIFIRE_IBLOCK_ID"])) {
    $arParams["CLASSIFIRE_IBLOCK_ID"] = 5;
}
if (!isset($arParams["CLASSIFIRE_PROPERTY_CODE"]) || empty($arParams["CLASSIFIRE_PROPERTY_CODE"])) {
    $arParams["CLASSIFIRE_PROPERTY_CODE"] = "FIRM";
}

global $USER;
global $APPLICATION;
if ($this->StartResultCache(false, $USER->GetGroups())) {
    $rsElements = CIBlockElement::GetList([], ["IBLOCK_ID" => $arParams["CLASSIFIRE_IBLOCK_ID"], "ACTIVE" => "Y"], false, false, ["ID", "NAME"]);
    $firms = [];
    $firmsIds = [];
    $classifireCount = 0;
    while ($item = $rsElements->Fetch()) {
        $firms[] = $item;
        $firmsIds[] = $item["ID"];
        $classifireCount++;
    }
    $rsElements = CIBlockElement::GetList(
        [],
        ["IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"], "ACTIVE" => "Y", "PROPERTY_".$arParams["CLASSIFIRE_PROPERTY_CODE"] => $firmsIds],
        false,
        false,
        ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PROPERTY_PRICE", "PROPERTY_MATERIAL", "PROPERTY_ARTNUMBER"]
    );
    $products = [];
    while ($obElement = $rsElements->GetNextElement()) {
        $item = $obElement->GetFields();
        $item[$arParams["CLASSIFIRE_PROPERTY_CODE"]] = $obElement->GetProperty($arParams["CLASSIFIRE_PROPERTY_CODE"])["VALUE"];
        $products[] = $item;
    }
    foreach ($firms as &$firm) {
        foreach ($products as $product) {
            if (in_array($firm["ID"], $product[$arParams["CLASSIFIRE_PROPERTY_CODE"]])) {
                $firm["PRODUCTS"][] = [
                    "NAME" => $product["NAME"],
                    "PRICE" => $product["PROPERTY_PRICE_VALUE"],
                    "MATERIAL" => $product["PROPERTY_MATERIAL_VALUE"],
                    "ARTNUMBER" => $product["PROPERTY_ARTNUMBER_VALUE"],
                ];
            }
        }
    }
    foreach ($firms as &$firm) {
        unset($firm["ID"]);
    }
    $arResult["ITEMS"] = $firms;
    $arResult["SECTION_COUNT"] = $classifireCount;
    $this->SetResultCacheKeys(["SECTION_COUNT"]);
    $this->includeComponentTemplate();
}
$APPLICATION->SetTitle(GetMessage("SIMPLECOMP_EXAM2_TITLE").$arResult["SECTION_COUNT"]);