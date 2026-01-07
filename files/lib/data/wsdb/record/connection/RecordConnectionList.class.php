<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\DatabaseObjectList;

/**
 * @extends DatabaseObjectList<RecordConnection>
 */
final class RecordConnectionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = RecordConnection::class;
}
