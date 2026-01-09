<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\CollectionDatabaseObject;
use wcf\data\wsdb\record\Record;

/**
 * @property-read int $connectionID
 * @property-read int $databaseID
 * @property-read int $recordID
 * @property-read int $referencedDatabaseID
 * @property-read int $referencedRecordID
 * @extends CollectionDatabaseObject<RecordConnectionCollection>
 */
final class RecordConnection extends CollectionDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'wsdb_record_connection';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'connectionID';

    public function getRecord(): Record
    {
        return $this->getCollection()->getRecord($this);
    }

    public function getReferencedRecord(): Record
    {
        return $this->getCollection()->getReferencedRecord($this);
    }
}
