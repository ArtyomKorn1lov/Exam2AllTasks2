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
if (!isset($arParams["TOP_COUNT"]) || empty($arParams["TOP_COUNT"])) {
    $arParams["TOP_COUNT"] = 100;
}
if (!isset($arParams["DETAIL_URL"]) || empty($arParams["DETAIL_URL"])) {
    $arParams["DETAIL_URL"] = "catalog_exam/#SECTION_ID#/#ELEMENT_CODE#";
}

global $USER;
global $APPLICATION;
global $CACHE_MANAGER;
$cFilter = false;
if (isset($_REQUEST["F"])) {
    $cFilter = true;
}
$page = 1;
if (isset($_REQUEST["PAGEN_1"])) {
    $page = (int)htmlspecialchars($_REQUEST["PAGEN_1"]);
}
$arAdminButtons = CIBlock::GetPanelButtons($arParams["PRODUCTS_IBLOCK_ID"]);
$this->AddIncludeAreaIcon(
    [
        "TITLE" => GetMessage("SIMPLECOMP_EXAM2_SUBMENU_TITLE"),
        "URL" => $arAdminButtons['submenu']['element_list']['ACTION_URL'],
        "IN_PARAMS_MENU" => true,
    ]
);
if ($this->StartResultCache(false, [$USER->GetGroups(), $cFilter, $page], "/servicesIblock")) {
    $CACHE_MANAGER->RegisterTag("iblock_id_3");
    $arPagination = false;
    if ($arParams["TOP_COUNT"] > 0) {
        $arPagination = [
            "nPageSize" => $arParams["TOP_COUNT"],
            "iNumPage" => $page
        ];
    }
    $rsElements = CIBlockElement::GetList([], ["IBLOCK_ID" => $arParams["CLASSIFIRE_IBLOCK_ID"], "ACTIVE" => "Y"], false, $arPagination, ["ID", "NAME"]);
    $firms = [];
    $firmsIds = [];
    $classifireCount = 0;
    $navString = $rsElements->GetPageNavStringEx($navComponentObject, GetMessage("SIMPLECOMP_EXAM2_NAV_MESSAGE"), '', 'Y');
    while ($item = $rsElements->Fetch()) {
        $firms[] = $item;
        $firmsIds[] = $item["ID"];
        $classifireCount++;
    }
    $arFilter = ["IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"], "ACTIVE" => "Y", "PROPERTY_".$arParams["CLASSIFIRE_PROPERTY_CODE"] => $firmsIds];
    if ($cFilter) {
        $arFilter[] = [
            "LOGIC" => "OR",
            ["<=PROPERTY_PRICE" => 1700, "PROPERTY_MATERIAL" => "Дерево, ткань"],
            ["<PROPERTY_PRICE" => 1500, "PROPERTY_MATERIAL" => "Металл, пластик"]
        ];
    }
    $rsElements = CIBlockElement::GetList(
        ["NAME" => "ASC", "SORT" => "ASC"],
        $arFilter,
        false,
        false,
        ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PROPERTY_PRICE", "PROPERTY_MATERIAL", "PROPERTY_ARTNUMBER", "DETAIL_PAGE_URL"]
    );
    $products = [];
    $rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
    $addElementFlag = true;
    $addLink = "";
    $addText = "";
    while ($obElement = $rsElements->GetNextElement()) {
        $item = $obElement->GetFields();
        $arButtons = CIBlock::GetPanelButtons(
            $item["IBLOCK_ID"],
            $item["ID"],
            0,
            array("SECTION_BUTTONS" => false, "SESSID" => false)
        );
        if ($addElementFlag) {
            $addText = $arButtons["edit"]["add_element"]["TEXT"];
            $addLink = $arButtons["edit"]["add_element"]["ACTION_URL"];
            $addElementFlag = false;
        }
        $item["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $item["EDIT_TEXT"] = $arButtons["edit"]["edit_element"]["TEXT"];
        $item["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
        $item["DELETE_TEXT"] = $arButtons["edit"]["delete_element"]["TEXT"];
        $item[$arParams["CLASSIFIRE_PROPERTY_CODE"]] = $obElement->GetProperty($arParams["CLASSIFIRE_PROPERTY_CODE"])["VALUE"];
        $products[] = $item;
    }
    foreach ($firms as &$firm) {
        foreach ($products as $product) {
            if (isset($product[$arParams["CLASSIFIRE_PROPERTY_CODE"]]) && !empty($product[$arParams["CLASSIFIRE_PROPERTY_CODE"]]) && in_array($firm["ID"], $product[$arParams["CLASSIFIRE_PROPERTY_CODE"]])) {
                $firm["PRODUCTS"][] = [
                    "ID" => $product["ID"],
                    "NAME" => $product["NAME"],
                    "PRICE" => $product["PROPERTY_PRICE_VALUE"],
                    "MATERIAL" => $product["PROPERTY_MATERIAL_VALUE"],
                    "ARTNUMBER" => $product["PROPERTY_ARTNUMBER_VALUE"],
                    "DETAIL_PAGE_URL" => $product["DETAIL_PAGE_URL"],
                    "EDIT_LINK" => $product["EDIT_LINK"],
                    "EDIT_TEXT" => $product["EDIT_TEXT"],
                    "DELETE_LINK" => $product["DELETE_LINK"],
                    "DELETE_TEXT" => $product["DELETE_TEXT"],
                ];
            }
        }
    }
    $arResult["ITEMS"] = $firms;
    $arResult["SECTION_COUNT"] = $classifireCount;
    $arResult["ADD_LINK"] = $addLink;
    $arResult["ADD_TEXT"] = $addText;
    $arResult["NAV_STRING"] = $navString;
    $this->SetResultCacheKeys(["SECTION_COUNT"]);
    $this->includeComponentTemplate();
}
$APPLICATION->SetTitle(GetMessage("SIMPLECOMP_EXAM2_TITLE").$arResult["SECTION_COUNT"]);