<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\DatabaseObjectList;
use wcf\system\wsdb\cache\runtime\RecordConnectionRuntimeCache;

/**
 * @extends DatabaseObjectList<RecordConnection>
 */
final class RecordConnectionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = RecordConnection::class;

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        RecordConnectionRuntimeCache::getInstance()->setObjects($this->getObjects());
    }
}
