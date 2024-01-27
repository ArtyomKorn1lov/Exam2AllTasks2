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
---
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE")?></b></p>
<?php if (isset($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) { ?>
<ul>
    <?php foreach ($arResult["ITEMS"] as $new) { ?>
    <li>
        <b><?=$new["NAME"]?></b> - <?=$new["DATE_ACTIVE_FROM"]?> (<?php foreach ($new["SECTIONS"] as $key => $section) { ?><?php if ($key === count($new["SECTIONS"])-1) { ?><?=$section?><?php } else { ?><?=$section?>, <?php } ?><?php } ?>)
        <ul>
        <?php foreach ($new["PRODUCTS"] as $product) { ?>
            <li>
                <?=$product["NAME"]?> - <?=$product["PRICE"]?> - <?=$product["MATERIAL"]?> - <?=$product["ARTNUMBER"]?>
            </li>
        <?php } ?>
        </ul>
    </li>
    <?php } ?>
</ul>
<?php } ?>