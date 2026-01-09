<?php

namespace wcf\system\event\listener;

use wcf\data\wsdb\database\DatabaseList;
use wcf\data\wsdb\record\connection\RecordConnectionList;
use wcf\page\WsdbRecordPage;
use wcf\system\WCF;

final class ConnectionWsdbRecordPageListener extends AbstractEventListener
{
    protected function onAssignVariables(WsdbRecordPage $eventObj): void
    {
        if (!$eventObj->getDatabase()->enableConnection) {
            return;
        }

        $connectionList = new RecordConnectionList();
        $connectionList->getConditionBuilder()->add('recordID = ?', [$eventObj->getRecord()->recordID]);
        $connectionList->sqlSelects .= "(
            SELECT  title
            FROM    wcf1_wsdb_record_content
            WHERE   recordID = wsdb_record_connection.referencedRecordID
                AND (
                        languageID IS NULL
                     OR languageID = " . WCF::getLanguage()->languageID . "
                    )
            LIMIT   1
        ) AS title";
        $connectionList->sqlOrderBy = 'title ASC';
        $connectionList->readObjects();

        $groupedConnections = [];
        foreach ($connectionList as $connection) {
            $record = $connection->getReferencedRecord();
            if (!$record->canRead()) {
                continue;
            }
            $groupedConnections[$connection->referencedDatabaseID][] = $record;
        }

        $databaseList = new DatabaseList();
        $databaseList->readObjects();

        WCF::getTPL()->assign([
            'groupedConnections' => $groupedConnections,
            'connectionDatabases' => $databaseList->getObjects(),
        ]);
    }
}
