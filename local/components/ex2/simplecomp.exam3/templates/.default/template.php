<?
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
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE")?></b></p>
<?php if (isset($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) { ?>
    <ul>
        <?php foreach ($arResult["ITEMS"] as $item) { ?>
            <li>
                [<?=$item["ID"]?>] - <?=$item["LOGIN"]?>
                <ul>
                    <?php foreach ($item["NEWS"] as $new) { ?>
                        <li>
                            - <?=$new["NAME"]?>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
