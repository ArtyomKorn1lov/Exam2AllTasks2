<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
?>
<p><?=GetMessage("SIMPLECOMP_EXAM2_TIME_MARK_TITLE")?><?=time()?></p>
<p>Фильтр: <a href="<?=$arResult["FILTER_URL"]?>">/exam2/ex2-48/?F=Y</a></p>
---
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE")?></b></p>
<?php $this->AddEditAction("add_element", $arResult['ADD_LINK'], $arResult["ADD_TEXT"]); ?>
<?php if(isset($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) { ?>
    <ul id="<?=$this->GetEditAreaId("add_element");?>">
    <?php foreach ($arResult["ITEMS"] as $section) { ?>
        <li>
            <b><?=$section["NAME"]?></b>
            <ul>
                <?php foreach ($section["PRODUCTS"] as $product) { ?>
                    <?
                    $this->AddEditAction($product['ID']."_".$section["ID"], $product['EDIT_LINK'], $product['EDIT_TEXT'], CIBlock::GetArrayByID($product["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($product['ID']."_".$section["ID"], $product['DELETE_LINK'], $product['DELETE_TEXT'], CIBlock::GetArrayByID($product["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <li id="<?=$this->GetEditAreaId($product['ID']."_".$section["ID"]);?>">
                        <?=$product["NAME"]?> -
                        <?=$product["PRICE"]?> -
                        <?=$product["MATERIAL"]?> -
                        <?=$product["ARTNUMBER"]?>
                         (<?=$product["DETAIL_PAGE_URL"]?>)
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
    </ul>
<?php } ?>
---
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_NAV_TITLE")?></b></p>
<?=$arResult["NAV_STRING"]?>
