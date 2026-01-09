<?php

namespace wcf\system\wsdb\cache\runtime;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\wsdb\record\connection\RecordConnection;
use wcf\data\wsdb\record\connection\RecordConnectionList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * @extends AbstractRuntimeCache<RecordConnection, RecordConnectionList>
 */
final class RecordConnectionRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = RecordConnectionList::class;

    /**
     * @param array<int, RecordConnection|DatabaseObjectDecorator<RecordConnection>> $objects
     */
    public function setObjects(array $objects): void
    {
        foreach ($objects as $object) {
            if ($object instanceof DatabaseObjectDecorator) {
                $object = $object->getDecoratedObject();
            }

            $this->objects[$object->getObjectID()] = $object;
            $this->objectIDs[$object->getObjectID()] = $object->getObjectID();
        }
    }
}
