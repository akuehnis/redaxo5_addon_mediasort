<?php

/**
 * @var rex_addon $this
 */
$addon = rex_addon::get('mediasort');

if (rex::isBackend() && rex::getUser() && rex_request::get('page') == 'mediapool/mediasort/mediasort') {
    // ui-sortable script
    rex_view::addJSFile($addon->getAssetsUrl('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js'));
    rex_view::addJSFile($addon->getAssetsUrl('mediasort.js'));
    rex_view::addJSFile($addon->getAssetsUrl('vendor/jquery.multisortable.js'));
    rex_view::addCSSFile($addon->getAssetsUrl('mediasort.css'));
}

