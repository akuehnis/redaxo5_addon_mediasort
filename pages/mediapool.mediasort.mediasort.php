<?php
/**
 * multiupload Addon.
 * @author Friends Of REDAXO
 * @package redaxo
 * @var rex_addon $this
 */
	
$addon = rex_addon::get('mediasort');
$media_manager = rex_addon::get('media_manager');

$has_priofield = (bool) count(rex_sql::factory()
    ->setTable(rex::getTable('metainfo_field'))
    ->setWhere(['name'=>'med_priority'])
    ->select("*")
    ->getArray());

if ($has_priofield){
    $sort = isset($_POST['sort']) ? $_POST['sort'] : null;
    if (is_array($sort)){
        // Datenbankvariable initialisieren
        $qry = 'SET @count=0';
        $sql = rex_sql::factory();
        $sql->setQuery($qry);

        // Spalte updaten
        $tableName = rex::getTable('media');
        $priorColumnName = 'med_priority';
        $qry = 'UPDATE ' . $tableName . ' SET ' . $priorColumnName . ' = ( SELECT @count := @count +1 )';
        $qry .= ' WHERE category_id=' . $rex_file_category;
        $qry .= " ORDER BY FIND_IN_SET (id, '".implode(',',$sort)."')";
        $sql->setQuery($qry);
    }
}

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

$files = !$has_priofield ? [] : rex_sql::factory()
    ->setQuery("SELECT * FROM `".rex::getTable('media')."`
    WHERE category_id=$rex_file_category 
    ORDER BY med_priority ASC")
    ->getArray();
ob_start();
?>
<div class="text-right">
<button type="button" id="sort_by_name_asc" class="btn btn-primary btn-small">Sort by Name ASC</button>
<button type="button" id="sort_by_name_desc" class="btn btn-primary btn-small">Sort by Name DESC</button>
</div>
<ul id="mediasort_category">
<?php foreach ($files as $file):?>
    <?php $file = (object) $file;
    // wenn datei fehlt
    if (!file_exists(rex_path::media($file->filename))) {
        $thumbnail = '<i class="rex-mime rex-mime-error" title="' . rex_i18n::msg('pool_file_does_not_exist') . '"></i><span class="sr-only">' . $file->originalname . '</span>';
    } else {
        $file_ext = substr(strrchr($file->filename, '.'), 1);
        $alt = $file->originalname;
        $icon_class = ' rex-mime-default';
        if (rex_media::isDocType($file_ext)) {
            $icon_class = ' rex-mime-' . $file_ext;
        }
        $thumbnail = '<i class="rex-mime' . $icon_class . '" title="' . $alt . '" data-extension="' . $file_ext . '"></i><span class="sr-only">' . $file->originalname . '</span>';

        if (rex_media::isImageType(rex_file::extension($file->filename))) {
            $thumbnail = '<img class="thumbnail" src="' . rex_url::media($file->filename) . '?buster=' . $file->updatedate . '" width="80" height="80" alt="' . $alt . '" title="' . $alt . '" />';
            if ($media_manager && rex_file::extension($file->filename) != 'svg') {
                $thumbnail = '<img class="thumbnail" src="' . rex_url::backendController(['rex_media_type' => 'rex_mediapool_preview', 'rex_media_file' => urlencode($file->filename), 'buster' => $file->updatedate]) . '" alt="' . $alt . '" title="' . $alt . '" />';
            }
        }
    }
?>
    <li class="mediasort_category_image" data-id="<?php echo $file->id;?>" data-originalname="<?php echo $alt;?>"><?php echo $thumbnail;?></li>
<?php endforeach; ?>
</ul>
<?php $panel = ob_get_clean();
if (!$has_priofield){
    $panel = '<div class="alert alert-danger">'.
        'Media-Metafeld mit dem Namen &quot;med_priority&quot; ist erforderlich'.
        '</div>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('pool_file_caption', $rex_file_category_name), false);
$fragment->setVar('options', $toolbar, false);
$fragment->setVar('content', $panel, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
