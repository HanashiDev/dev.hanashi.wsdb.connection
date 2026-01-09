<?php

use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    DatabaseTable::create('wcf1_wsdb_record_connection')
        ->columns([
            ObjectIdDatabaseTableColumn::create('connectionID'),
            NotNullInt10DatabaseTableColumn::create('databaseID'),
            NotNullInt10DatabaseTableColumn::create('recordID'),
            NotNullInt10DatabaseTableColumn::create('referencedDatabaseID'),
            NotNullInt10DatabaseTableColumn::create('referencedRecordID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['connectionID']),
            DatabaseTableIndex::create('databaseReference')
                ->columns(['databaseID', 'recordID', 'referencedDatabaseID', 'referencedRecordID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['databaseID'])
                ->referencedTable('wcf1_wsdb_database')
                ->referencedColumns(['databaseID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['referencedDatabaseID'])
                ->referencedTable('wcf1_wsdb_database')
                ->referencedColumns(['databaseID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['recordID'])
                ->referencedTable('wcf1_wsdb_record')
                ->referencedColumns(['recordID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['referencedRecordID'])
                ->referencedTable('wcf1_wsdb_record')
                ->referencedColumns(['recordID'])
                ->onDelete('CASCADE'),
        ]),
    PartialDatabaseTable::create('wcf1_wsdb_database')
        ->columns([
            TinyintDatabaseTableColumn::create('enableConnection')
                ->notNull()
                ->defaultValue(0),
        ]),
];
