<?php
/**
 * Addon Mediasort.
 *
 * @author Adrian Kühnis
 *
 */

rex_sql::factory()
    ->setQuery("ALTER TABLE `".rex::getTable('media')."` 
    DROP COLUMN `priority`")
    ;


