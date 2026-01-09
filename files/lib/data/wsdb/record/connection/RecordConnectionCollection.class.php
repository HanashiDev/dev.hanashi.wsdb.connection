<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\DatabaseObjectCollection;
use wcf\data\wsdb\record\Record;
use wcf\system\wsdb\cache\runtime\RecordRuntimeCache;

/**
 * @extends DatabaseObjectCollection<RecordConnection>
 */
final class RecordConnectionCollection extends DatabaseObjectCollection
{
    private bool $recordsLoaded = false;

    public function getRecord(RecordConnection $connection): Record
    {
        $this->loadRecords();

        return RecordRuntimeCache::getInstance()->getObject($connection->recordID);
    }

    public function getReferencedRecord(RecordConnection $connection): Record
    {
        $this->loadRecords();

        return RecordRuntimeCache::getInstance()->getObject($connection->referencedRecordID);
    }

    private function loadRecords(): void
    {
        if ($this->recordsLoaded) {
            return;
        }
        $this->recordsLoaded = true;

        $recordIDs = [];
        foreach ($this->getObjects() as $object) {
            $recordIDs[] = $object->recordID;
            $recordIDs[] = $object->referencedRecordID;
        }

        if ($recordIDs !== []) {
            RecordRuntimeCache::getInstance()->cacheObjectIDs($recordIDs);
        }
    }
}
