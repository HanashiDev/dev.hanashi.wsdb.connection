<?php

namespace wcf\data\wsdb\record\connection;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

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

    #[\Override]
    public function delete(): int
    {
        $sql = "DELETE FROM wcf1_wsdb_record_connection WHERE recordID = ? AND referencedRecordID = ?";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($this->getObjects() as $object) {
            $statement->execute([$object->referencedRecordID, $object->recordID]);
        }

        return parent::delete();
    }
}
