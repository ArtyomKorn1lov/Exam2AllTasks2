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

if (!isset($arParams["NEWS_IBLOCK_ID"]) || empty($arParams["NEWS_IBLOCK_ID"])) {
    $arParams["NEWS_IBLOCK_ID"] = 1;
}
if (!isset($arParams["PROPERTY_CODE_AUTHOR"]) || empty($arParams["PROPERTY_CODE_AUTHOR"])) {
    $arParams["PROPERTY_CODE_AUTHOR"] = "AUTHOR";
}
if (!isset($arParams["UF_CODE_AUTHOR"]) || empty($arParams["UF_CODE_AUTHOR"])) {
    $arParams["UF_CODE_AUTHOR"] = "UF_AUTHOR_TYPE";
}

global $USER;
global $APPLICATION;

if ($USER->IsAuthorized()) {
    if ($this->StartResultCache(false, [$USER->GetID()])) {
        $rsUser = CUser::GetByID($USER->GetID());
        $curUserFields = $rsUser->Fetch();
        $arUsers = [];
        $arUsersIds = [];
        $rsUsers = CUser::GetList(
            [],
            "asc",
            ["!ID" => $USER->GetID(), $arParams["UF_CODE_AUTHOR"] => $curUserFields[$arParams["UF_CODE_AUTHOR"]], "ACTIVE" => "Y"],
            ["SELECT" => [$arParams["UF_CODE_AUTHOR"]]]
        );
        while ($item = $rsUsers->Fetch()) {
            $arUsers[] = [
                "ID" => $item["ID"],
                "LOGIN" => $item["LOGIN"]
            ];
            $arUsersIds[] = $item["ID"];
        }
        if (isset($arUsersIds) && !empty($arUsersIds)) {
            $rsElements = CIBlockElement::GetList(
                [],
                ["IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"], "ACTIVE" => "Y", "PROPERTY_" . $arParams["PROPERTY_CODE_AUTHOR"] => $arUsersIds],
                false,
                false,
                ["ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME"]
            );
            $arNews = [];
            $count = 0;
            while ($obItem = $rsElements->GetNextElement()) {
                $item = $obItem->GetFields();
                $item["AUTHORS"] = $obItem->GetProperty($arParams["PROPERTY_CODE_AUTHOR"])["VALUE"];
                $arNews[] = $item;
                $count++;
            }
            foreach ($arUsers as &$user) {
                foreach ($arNews as $arNew) {
                    if (isset($arNew["AUTHORS"]) && !empty($arNew["AUTHORS"]) && in_array((int)$user["ID"], $arNew["AUTHORS"])) {
                        $user["NEWS"][] = [
                            "NAME" => $arNew["NAME"]
                        ];
                    }
                }
            }
            $arResult["ITEMS"] = $arUsers;
            $arResult["COUNT"] = $count;
            $this->SetResultCacheKeys(["COUNT"]);
            $this->includeComponentTemplate();
        }
    }
    $APPLICATION->SetTitle(GetMessage("SIMPLECOMP_EXAM2_TITLE").$arResult["COUNT"]);
}