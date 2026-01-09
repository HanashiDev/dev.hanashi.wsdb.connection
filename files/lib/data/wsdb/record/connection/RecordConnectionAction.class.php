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

    #[\Override]
    public function create(): RecordConnection
    {
        RecordConnectionEditor::create([
            'recordID' => $this->parameters['data']['referencedRecordID'],
            'databaseID' => $this->parameters['data']['referencedDatabaseID'],
            'referencedRecordID' => $this->parameters['data']['recordID'],
            'referencedDatabaseID' => $this->parameters['data']['databaseID'],
        ]);

        return parent::create();
    }
}
