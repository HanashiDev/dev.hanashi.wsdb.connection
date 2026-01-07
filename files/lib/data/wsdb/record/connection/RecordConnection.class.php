<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\DatabaseObject;

/**
 * @property-read int $connectionID
 * @property-read int $databaseID
 * @property-read int $recordID
 * @property-read int $referencedDatabaseID
 * @property-read int $referencedRecordID
 */
final class RecordConnection extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'wsdb_record_connection';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'connectionID';
}
