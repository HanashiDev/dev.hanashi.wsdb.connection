<?php

namespace wcf\system\event\listener;

use wcf\data\DatabaseObject;
use wcf\data\wsdb\record\Record;
use wcf\event\wsdb\interaction\user\RecordInteractionCollecting;
use wcf\form\WsdbConnectionAddForm;
use wcf\page\WsdbConnectionListPage;
use wcf\system\interaction\AbstractInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

final class ConnectionWsdbRecordInteractionCollectingListener
{
    public function __invoke(RecordInteractionCollecting $event): void
    {
        $event->provider->addInteractionBefore(
            new class('connection', static fn (Record $record) => $record->canEdit() && $record->getDatabase()->enableConnection) extends AbstractInteraction {
                #[\Override]
                public function render(DatabaseObject $object): string
                {
                    \assert($object instanceof Record);

                    return \sprintf(
                        '<a href="%s">%s</a>',
                        StringUtil::encodeHTML(
                            LinkHandler::getInstance()->getControllerLink(WsdbConnectionListPage::class, [
                                '__database' => $object->getDatabase()->path,
                                'object' => $object,
                            ])
                        ),
                        WCF::getLanguage()->get('wsdb.record.connections')
                    );
                }
            },
            'edit'
        );
        $event->provider->addInteractionBefore(
            new class('connectionAdd', static fn (Record $record) => $record->canEdit() && $record->getDatabase()->enableConnection) extends AbstractInteraction {
                #[\Override]
                public function render(DatabaseObject $object): string
                {
                    \assert($object instanceof Record);

                    return \sprintf(
                        '<a href="%s">%s</a>',
                        StringUtil::encodeHTML(
                            LinkHandler::getInstance()->getControllerLink(WsdbConnectionAddForm::class, [
                                '__database' => $object->getDatabase()->path,
                                'object' => $object,
                            ])
                        ),
                        WCF::getLanguage()->get('wsdb.record.connection.add')
                    );
                }
            },
            'edit'
        );
    }
}
