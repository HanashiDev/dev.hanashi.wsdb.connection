<?php

namespace wcf\system\wsdb\interaction\user;

use wcf\data\wsdb\record\connection\RecordConnection;
use wcf\event\wsdb\interaction\user\ConnectionInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

final class ConnectionInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction("hanashi/wsdb/records/connections/%s"),
        ]);

        EventHandler::getInstance()->fire(
            new ConnectionInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return RecordConnection::class;
    }
}
