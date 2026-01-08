<?php

namespace wcf\system\event\listener;

use wcf\acp\form\WsdbDatabaseEditForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\BooleanFormField;

final class ConnectionWsdbDatabaseEditFormListener extends AbstractEventListener
{
    protected function onCreateForm(WsdbDatabaseEditForm $eventObj): void
    {
        $settings = $eventObj->form->getNodeById('settings');
        \assert($settings instanceof FormContainer);

        $settings->appendChild(
            BooleanFormField::create('enableConnection')
                ->label('dev.hanashi.wsdb.connection.enableConnection')
        );
    }
}
