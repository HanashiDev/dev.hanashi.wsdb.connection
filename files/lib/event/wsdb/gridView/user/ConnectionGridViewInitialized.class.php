<?php

namespace wcf\event\wsdb\gridView\user;

use wcf\event\IPsr14Event;
use wcf\system\wsdb\gridView\user\ConnectionGridView;

final class ConnectionGridViewInitialized implements IPsr14Event
{
    public function __construct(public readonly ConnectionGridView $gridView)
    {
    }
}
