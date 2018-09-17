<?php
/**
 * multiupload Addon.
 * @author Friends Of REDAXO
 * @package redaxo
 * @var rex_addon $this
 */
	
$addon = rex_addon::get('mediasort');

// ***** kategorie auswahl
$db = rex_sql::factory();
$file_cat = $db->getArray('SELECT * FROM ' . rex::getTablePrefix() . 'media_category ORDER BY name ASC');

// ***** select bauen
$sel_media = new rex_media_category_select($check_perm = false);
$sel_media->setId('rex_file_category');
$sel_media->setName('rex_file_category');
$sel_media->setSize(1);
$sel_media->setSelected($rex_file_category);
$sel_media->setAttribute('onchange', 'this.form.submit();');
$sel_media->setAttribute('class', 'selectpicker');
$sel_media->setAttribute('data-live-search', 'true');

if (rex::getUser()->getComplexPerm('media')->hasAll()) {
    $sel_media->addOption(rex_i18n::msg('pool_kats_no'), '0');
}
$formElements = [];
$n = [];
$n['before'] = $sel_media->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$toolbar = '<div class="rex-truncate-dropdown">' . $fragment->parse('core/form/input_group.php') . '</div>';

$toolbar = '
<div class="navbar-form navbar-right">
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $arg_fields . '
    <div class="form-group">
    ' . $toolbar . '
    </div>
</form>
</div>';

$files = rex_sql::factory()
    ->setTable(rex::getTable('media'))
    ->setWhere(['category_id'=>$rex_file_category])
    ->select("*")
    ->getArray();
ob_start();
?>
<ul class="mediasort_category">
<?php foreach ($files as $file):?>
    <?php $file = (object) $file;?>
<li class="mediasort_category_image" data-id="<?php echo $file->id;?>">
    <img class="thumbnail"
        src="index.php?rex_media_type=rex_mediapool_preview&rex_media_file=<?php echo urlencode($file->filename);?>"
        title="<?php echo $file->originalname;?>"
        />
        
    </li>
<?php endforeach; ?>
</ul>
<?php $panel = ob_get_clean();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('pool_file_caption', $rex_file_category_name), false);
$fragment->setVar('options', $toolbar, false);
$fragment->setVar('content', $panel, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
