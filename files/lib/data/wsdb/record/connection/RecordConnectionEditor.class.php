<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\DatabaseObjectEditor;

/**
 * @mixin       RecordConnection
 * @extends DatabaseObjectEditor<RecordConnection>
 */
final class RecordConnectionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = RecordConnection::class;
}
