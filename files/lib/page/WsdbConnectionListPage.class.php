<?php

namespace wcf\page;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\wsdb\gridView\user\ConnectionGridView;
use wcf\system\wsdb\page\WsdbPageLocationManager;

/**
 * @extends AbstractGridViewPage<ConnectionGridView>
 */
final class WsdbConnectionListPage extends AbstractGridViewPage implements IWsdbPage
{
    use TWsdbRecordPage;

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        $this->canViewRecords();

        $this->readRecord();

        if (!$this->getRecord()->canEdit() || !$this->getDatabase()->enableConnection) {
            throw new PermissionDeniedException();
        }
    }

    #[\Override]
    protected function createGridView(): ConnectionGridView
    {
        return new ConnectionGridView($this->getRecord()->recordID);
    }

    #[\Override]
    public function readData()
    {
        parent::readData();

        WsdbPageLocationManager::setLocation(
            $this->getDatabase(),
            $this->getRecord()->getParentCategories(),
            $this->getRecord()->getCategory(),
            $this->getRecord()
        );
    }

    #[\Override]
    protected function getBaseUrlParameters(): array
    {
        return [
            '__database' => $this->getDatabase()->path,
            'object' => $this->getRecord(),
        ];
    }
}
