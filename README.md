# Redaxo5 Addon Mediasort

Das Addon ermöglicht die Sortierung von Medien in einer Medien-Kategorie. 

Um die sortierten Dateien anzueigen, kann man das folgende Modul als Beispiel verwenden.

Eingabe
```
<?php
$rex_media_category = (int)"REX_VALUE[1]";
$sel_media = new rex_media_category_select($check_perm = false);
$sel_media->setId('rex_file_category');
$sel_media->setName('REX_INPUT_VALUE[1]');
$sel_media->setSize(1);
$sel_media->setSelected($rex_media_category);
$sel_media->setAttribute('onchange', 'this.form.submit();');
$sel_media->setAttribute('class', 'selectpicker');
$sel_media->setAttribute('data-live-search', 'true');
$sel_media->show();
?>
```

Ausgabe

```
<?php
$files = rex_sql::factory()
	->setQuery("SELECT filename
    FROM `".rex::getTable('media')."`
    WHERE category_id=".intval("REX_VALUE[1]")."
    AND filetype IN ('image/jpeg', 'image/png', 'image/gif')
    ORDER BY priority")
    ->getArray();

$files = array_map(function($row){
    return $row['filename'];
}, $files);
foreach ($files as $file) {
   echo '<img src="index.php?rex_media_type=rex_mediapool_preview&rex_media_file='.urlencode($file).'" />';
}
?>
```



Screenshots:

![Drag and drop to sort](/assets/mediasort_drag_drop.png?raw=true "Sort images by Drag & Drop")
