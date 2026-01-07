<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * @extends AbstractDatabaseObjectAction<RecordConnection, RecordConnectionEditor>
 */
final class RecordConnectionAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public $className = RecordConnectionEditor::class;
}
