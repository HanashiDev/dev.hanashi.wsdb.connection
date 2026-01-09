<?php

namespace wcf\event\wsdb\interaction\user;

use wcf\event\IPsr14Event;
use wcf\system\wsdb\interaction\user\ConnectionInteractions;

final class ConnectionInteractionCollecting implements IPsr14Event
{
    public function __construct(public readonly ConnectionInteractions $provider)
    {
    }
}
