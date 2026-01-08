<?php

use wcf\command\wsdb\database\SetConnectionDatabasePages;
use wcf\event\wsdb\database\DatabaseCreated;
use wcf\event\wsdb\interaction\user\RecordInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\event\listener\ConnectionWsdbRecordInteractionCollectingListener;

return static function (): void {
    EventHandler::getInstance()->register(
        RecordInteractionCollecting::class,
        ConnectionWsdbRecordInteractionCollectingListener::class
    );

    EventHandler::getInstance()->register(DatabaseCreated::class, static function (DatabaseCreated $event): void {
        (new SetConnectionDatabasePages($event->database))();
    });
};
