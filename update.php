<?php
/**
 * Addon Mediasort.
 *
 * @author Adrian Kühnis
 *
 */

rex_sql::factory()
    ->setQuery("ALTER TABLE `".rex::getTable('media')."` 
    ADD COLUMN `priority` INT(11) NOT NULL DEFAULT 1")
    ;

