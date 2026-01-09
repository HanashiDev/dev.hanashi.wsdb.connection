<?php

namespace wcf\form;

use wcf\data\wsdb\database\Database;
use wcf\data\wsdb\database\DatabaseList;
use wcf\data\wsdb\record\connection\RecordConnection;
use wcf\data\wsdb\record\connection\RecordConnectionAction;
use wcf\data\wsdb\record\connection\RecordConnectionList;
use wcf\data\wsdb\record\RecordList;
use wcf\page\IWsdbPage;
use wcf\page\TWsdbRecordPage;
use wcf\page\WsdbConnectionListPage;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormChildNode;
use wcf\system\form\builder\IFormDocument;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\system\wsdb\page\WsdbPageLocationManager;

/**
 * @extends AbstractFormBuilderForm<RecordConnection>
 */
class WsdbConnectionAddForm extends AbstractFormBuilderForm implements IWsdbPage
{
    use TWsdbRecordPage;

    /**
     * @inheritDoc
     */
    public $objectActionClass = RecordConnectionAction::class;

    /**
     * @var Database[]
     */
    private array $databases;

    /**
     * @var array<int, array<int, string>>
     */
    private array $groupedRecords;

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
    protected function setFormAction()
    {
        $this->form->action(LinkHandler::getInstance()->getControllerLink(
            static::class,
            [
                '__database' => $this->getDatabase()->path,
                'object' => $this->getRecord(),
            ]
        ));
    }

    #[\Override]
    protected function createForm(): void
    {
        parent::createForm();

        $this->form->appendChildren([
            FormContainer::create('data')
                ->appendChildren([
                    SelectFormField::create('referencedDatabaseID')
                        ->label('dev.hanashi.wsdb.connection.database')
                        ->options($this->getDatabaseOptions())
                        ->required(),
                    ...$this->getRecordSelects(),
                ]),
        ]);
    }

    #[\Override]
    protected function finalizeForm(): void
    {
        parent::finalizeForm();

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'finalizeRecords',
                function (IFormDocument $document, array $parameters): array {
                    if (
                        isset(
                            $parameters['data']['referencedRecordID' . $parameters['data']['referencedDatabaseID']]
                        )
                    ) {
                        $parameters['data']['referencedRecordID']
                            = $parameters['data']['referencedRecordID' . $parameters['data']['referencedDatabaseID']];
                        unset($parameters['data']['referencedRecordID' . $parameters['data']['referencedDatabaseID']]);
                    }

                    $parameters['data']['databaseID'] = $this->getDatabase()->databaseID;
                    $parameters['data']['recordID'] = $this->record->recordID;

                    return $parameters;
                }
            )
        );
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
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'connectionListLink' => LinkHandler::getInstance()->getControllerLink(
                WsdbConnectionListPage::class,
                [
                    '__database' => $this->getRecord()->getDatabase()->path,
                    'object' => $this->getRecord(),
                ]
            ),
        ]);
    }

    /**
     * @return IFormChildNode[]
     */
    private function getRecordSelects(): array
    {
        $groupedRecords = $this->getRecordsGroupedByDatabases();
        $recordID = $this->getRecord()->recordID;
        \uasort($groupedRecords[5], static fn (string $a, string $b): int => ($a == $b) ? 0 : (($a < $b) ? -1 : 1));

        return \array_map(
            static function (Database $database) use ($groupedRecords, $recordID): IFormChildNode {
                $options = $groupedRecords[$database->databaseID] ?? [];
                \uasort(
                    $options,
                    static fn (string $a, string $b): int => ($a == $b) ? 0 : (($a < $b) ? -1 : 1)
                );

                return SingleSelectionFormField::create('referencedRecordID' . $database->databaseID)
                    ->label('dev.hanashi.wsdb.connection.record')
                    ->options($options)
                    ->filterable()
                    ->addDependency(
                        ValueFormFieldDependency::create('recordDependency' . $database->databaseID)
                            ->fieldId('referencedDatabaseID')
                            ->values([$database->databaseID])
                    )
                    ->addValidator(
                        new FormFieldValidator(
                            'checkDatabaseReference',
                            static function (SingleSelectionFormField $formField) use ($recordID): void {
                                $list = new RecordConnectionList();
                                $list->getConditionBuilder()->add(
                                    "
                                        (recordID = ? AND referencedRecordID = ?)
                                        OR (recordID = ? AND referencedRecordID = ?)
                                    ",
                                    [
                                        $recordID,
                                        $formField->getValue(),
                                        $formField->getValue(),
                                        $recordID,
                                    ]
                                );

                                if ($list->countObjects() > 0) {
                                    $formField->addValidationError(
                                        new FormFieldValidationError(
                                            'alreadyConnected',
                                            'dev.hanashi.wsdb.connection.alreadyConnected'
                                        )
                                    );
                                }
                            }
                        )
                    )
                    ->required();
            },
            $this->getDatabases()
        );
    }

    /**
     * @return Database[]
     */
    private function getDatabases(): array
    {
        if (!isset($this->databases)) {
            $databaseList = new DatabaseList();
            $databaseList->sqlOrderBy = 'name ASC';
            $databaseList->readObjects();

            $databases = \array_filter(
                $databaseList->getObjects(),
                static fn (Database $database): bool => $database->enableConnection
            );
            $this->databases = $databases;
        }

        return $this->databases;
    }

    /**
     * @return array<int, string>
     */
    private function getDatabaseOptions(): array
    {
        $databases = [];
        $groupedRecords = $this->getRecordsGroupedByDatabases();
        foreach ($this->getDatabases() as $database) {
            if (!isset($groupedRecords[$database->databaseID]) || $groupedRecords[$database->databaseID] === []) {
                continue;
            }
            $databases[$database->databaseID] = $database->getTitle();
        }

        return $databases;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function getRecordsGroupedByDatabases(): array
    {
        if (!isset($this->groupedRecords)) {
            $recordList = new RecordList();
            $recordList->getConditionBuilder()->add('recordID <> ?', [$this->record->recordID]);
            $recordList->readObjects();

            $groupedRecords = [];
            foreach ($recordList as $record) {
                if (!$record->canEdit()) {
                    continue;
                }
                $groupedRecords[$record->databaseID][$record->recordID] = $record->getTitle();
            }
            $this->groupedRecords = $groupedRecords;
        }

        return $this->groupedRecords;
    }
}
