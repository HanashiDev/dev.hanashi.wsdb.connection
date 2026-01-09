<?php

use wcf\command\wsdb\database\SetConnectionDatabasePages;
use wcf\event\endpoint\ControllerCollecting;
use wcf\event\wsdb\database\DatabaseCreated;
use wcf\event\wsdb\interaction\user\RecordInteractionCollecting;
use wcf\system\endpoint\controller\hanashi\wsdb\records\connections\DeleteConnection;
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

    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event): void {
            $event->register(new DeleteConnection());
        }
    );
};
