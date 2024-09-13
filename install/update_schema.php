<?php

rex_sql_table::get(rex::getTable('media_manager_type_group'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('description', 'text'))
    ->ensureColumn(new rex_sql_column('fallback_id', 'varchar(191)'))
    ->ensure();

rex_sql_table::get(rex::getTable('media_manager_type_meta'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('group_id', 'int(10) unsigned'))
    ->ensureColumn(new rex_sql_column('type', 'varchar(191)'))
    ->ensureColumn(new rex_sql_column('ratio', 'varchar(191)'))
    ->ensureColumn(new rex_sql_column('min_width', 'varchar(191)', false, '1px'))
    ->ensureColumn(new rex_sql_column('max_width', 'varchar(191)', false, ''))
    ->ensureColumn(new rex_sql_column('prio', 'int(11)'))
    ->ensure();
