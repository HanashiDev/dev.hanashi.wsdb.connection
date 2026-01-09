<?php

namespace wcf\system\wsdb\gridView\user;

use wcf\data\DatabaseObjectList;
use wcf\data\wsdb\database\I18nDatabaseList;
use wcf\data\wsdb\record\connection\RecordConnection;
use wcf\data\wsdb\record\connection\RecordConnectionList;
use wcf\data\wsdb\record\Record;
use wcf\form\WsdbConnectionAddForm;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\request\LinkHandler;
use wcf\system\view\filter\ObjectIdFilter;
use wcf\system\view\filter\SelectFilter;
use wcf\system\view\filter\TextFilter;
use wcf\system\WCF;
use wcf\system\wsdb\cache\runtime\RecordRuntimeCache;

/**
 * @extends AbstractGridView<RecordConnection, RecordConnectionList>
 */
final class ConnectionGridView extends AbstractGridView
{
    public function __construct(public readonly int $recordID)
    {
        $this->addColumns([
            GridViewColumn::for('connectionID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(ObjectIdFilter::class)
                ->sortable(),
            GridViewColumn::for('title')
                ->label('dev.hanashi.wsdb.connection.record')
                ->titleColumn()
                ->filter(
                    new class('title', 'dev.hanashi.wsdb.connection.record') extends TextFilter {
                        #[\Override]
                        public function applyFilter(DatabaseObjectList $list, string $value): void
                        {
                            $list->getConditionBuilder()->add(
                                "
                                    wsdb_record_connection.referencedRecordID IN (
                                        SELECT recordID FROM wcf1_wsdb_record_content WHERE title LIKE ?
                                    )
                                ",
                                ['%' . WCF::getDB()->escapeLikeValue($value) . '%']
                            );
                        }
                    }
                )
                ->sortable(true, 'title'),
            GridViewColumn::for('databaseName')
                ->label('dev.hanashi.wsdb.connection.database')
                ->renderer(new PhraseColumnRenderer())
                ->filter(
                    new SelectFilter(
                        $this->getDatabases(),
                        'referencedDatabaseID',
                        'dev.hanashi.wsdb.connection.database',
                        'referencedDatabaseID'
                    )
                )
                ->sortable(true, 'databaseName'),
        ]);

        $this->setDefaultSortField('title');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return $this->getRecord()->canEdit() && $this->getRecord()->getDatabase()->enableConnection;
    }

    #[\Override]
    public function getParameters(): array
    {
        return [
            'recordID' => $this->recordID,
        ];
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new RecordConnectionList();
        $list->getConditionBuilder()->add('wsdb_record_connection.recordID = ?', [$this->recordID]);
        $list->sqlSelects .= "(
            SELECT  title
            FROM    wcf1_wsdb_record_content
            WHERE   recordID = wsdb_record_connection.referencedRecordID
                AND (
                        languageID IS NULL
                     OR languageID = " . WCF::getLanguage()->languageID . "
                    )
            LIMIT   1
        ) AS title,
        (
            SELECT  name
            FROM    wcf1_wsdb_database
            WHERE   databaseID = wsdb_record_connection.referencedDatabaseID
        ) AS databaseName";

        return $list;
    }

    public function getConnectionAddFormLink(): string
    {
        return LinkHandler::getInstance()->getControllerLink(
            WsdbConnectionAddForm::class,
            [
                '__database' => $this->getRecord()->getDatabase()->path,
                'object' => $this->getRecord(),
            ]
        );
    }

    public function getRecord(): Record
    {
        return RecordRuntimeCache::getInstance()->getObject($this->recordID);
    }

    /**
     * @return array<int, string>
     */
    private function getDatabases(): array
    {
        $list = new I18nDatabaseList();
        $list->sqlOrderBy = 'nameI18n ASC';
        $list->readObjects();

        $databases = [];
        foreach ($list as $database) {
            if (!$database->getPermission('canViewRecord')) {
                continue;
            }
            $databases[$database->databaseID] = $database->getTitle();
        }

        return $databases;
    }
}
