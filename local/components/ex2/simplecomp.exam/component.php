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
if (!isset($arParams["NEWS_IBLOCK_ID"]) || empty($arParams["NEWS_IBLOCK_ID"])) {
    $arParams["NEWS_IBLOCK_ID"] = 1;
}
if (!isset($arParams["UF_CODE"]) || empty($arParams["UF_CODE"])) {
    $arParams["UF_CODE"] = "UF_NEWS_LINK";
}

global $APPLICATION;
if ($this->StartResultCache()) {
    /** Получаем все активные новости */
    $rsElements = CIBlockElement::GetList(
        [],
        ["IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"], "ACTIVE" => "Y"],
        false,
        false,
        ["ID", "NAME", "DATE_ACTIVE_FROM"]
    );
    $arNews = [];
    $arNewsIds = [];
    while ($item = $rsElements->Fetch()) {
        $arNews[] = $item;
        $arNewsIds[] = $item["ID"];
    }
    /** Получаем разделы, к которым привязаны новости */
    $rsElements = CIBlockSection::GetList(
        [],
        ["IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"], "ACTIVE" => "Y", $arParams["UF_CODE"] => $arNewsIds],
        false,
        ["ID", "NAME", $arParams["UF_CODE"]],
        false
    );
    $arSection = [];
    $arSectionIds = [];
    while ($section = $rsElements->Fetch()) {
        $arSection[] = $section;
        $arSectionIds[] = $section["ID"];
    }
    /** Компонуем разделы к новостям */
    foreach ($arNews as &$new) {
        foreach ($arSection as $section) {
            if (in_array($new["ID"], $section[$arParams["UF_CODE"]])) {
                $new["SECTIONS"][] = $section["NAME"];
                $new["SECTIONS_IDS"][] = $section["ID"];
            }
        }
    }
    /** Получаем список товаров, которые привязаны к разделам */
    $rsElements = CIBlockElement::GetList(
        [],
        ["IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"], "ACTIVE" => "Y", "IBLOCK_SECTION_ID" => $arSectionIds ],
        false,
        false,
        ["ID", "NAME", "PROPERTY_ARTNUMBER", "PROPERTY_MATERIAL", "PROPERTY_PRICE", "IBLOCK_SECTION_ID"]
    );
    $arProducts = [];
    $productCount = 0;
    while ($item = $rsElements->Fetch()) {
        $arProducts[] = $item;
        $productCount++;
    }
    /** Компонуем товары к новостям, если они есть в разделах */
    foreach ($arNews as &$new) {
        foreach ($arProducts as $product) {
            if (in_array($product["IBLOCK_SECTION_ID"], $new["SECTIONS_IDS"])) {
                $new["PRODUCTS"][] = [
                    "NAME" => $product["NAME"],
                    "ARTNUMBER" => $product["PROPERTY_ARTNUMBER_VALUE"],
                    "MATERIAL" => $product["PROPERTY_MATERIAL_VALUE"],
                    "PRICE" => $product["PROPERTY_PRICE_VALUE"],
                ];
            }
        }
    }
    /** Избавимся от лишних ключей массива новостей */
    foreach ($arNews as &$new) {
        unset($new["ID"]);
        unset($new["SECTIONS_IDS"]);
    }
    $arResult["ITEMS"] = $arNews;
    $arResult["PRODUCT_COUNT"] = $productCount;
    $this->SetResultCacheKeys(["PRODUCT_COUNT"]);
    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage("SIMPLECOMP_EXAM2_TITLE").$arResult["PRODUCT_COUNT"]);