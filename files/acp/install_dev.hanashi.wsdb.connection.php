<?php

use wcf\command\wsdb\database\SetConnectionDatabasePages;
use wcf\data\wsdb\database\DatabaseList;

$databaseList = new DatabaseList();
$databaseList->readObjects();

foreach ($databaseList as $database) {
    (new SetConnectionDatabasePages($database, $this->installation->getPackageID()))();
}
