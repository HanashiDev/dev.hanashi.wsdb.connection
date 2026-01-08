<?php

namespace wcf\command\wsdb\database;

use wcf\data\package\PackageCache;
use wcf\data\page\PageAction;
use wcf\data\page\PageCache;
use wcf\data\wsdb\database\Database;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

final class SetConnectionDatabasePages
{
    public function __construct(private readonly Database $database, private ?int $packageID = null)
    {
    }

    public function __invoke(): void
    {
        if ($this->packageID === null) {
            $this->packageID = PackageCache::getInstance()->getPackageID('dev.hanashi.wsdb.connection');
        }
        $this->setConnectionAddForm();
        $this->setConnectionListPage();
    }

    private function setConnectionAddForm(): void
    {
        $pageContents = [];
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $pageContents[$language->languageID] = [
                'title' => $language->get('wsdb.record.connection.add'),
            ];
        }

        $identifier = "dev.hanashi.wsdb.{$this->database->identifier}.ConnectionAdd";
        $page = PageCache::getInstance()->getPageByIdentifier($identifier);
        if ($page !== null) {
            $action = new PageAction([$page], 'update', [
                'data' => [],
                'content' => $pageContents,
            ]);
            $action->executeAction();
        } else {
            $action = new PageAction([], 'create', [
                'data' => [
                    'identifier' => $identifier,
                    'name' => "{$this->database->getTitle()}: " . WCF::getLanguage()->get('wsdb.record.connection.add'),
                    'pageType' => 'system',
                    'originIsSystem' => 1,
                    'packageID' => $this->packageID,
                    'applicationPackageID' => 1,
                    'controller' => "wcf\\form\\{$this->database->identifier}_WsdbConnectionAddForm",
                    'requireObjectID' => 1,
                ],
                'content' => $pageContents,
            ]);
            $action->executeAction();
        }
    }

    private function setConnectionListPage(): void
    {
        $pageContents = [];
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $pageContents[$language->languageID] = [
                'title' => $language->get('wsdb.record.connections'),
            ];
        }

        $identifier = "dev.hanashi.wsdb.{$this->database->identifier}.ConnectionList";
        $page = PageCache::getInstance()->getPageByIdentifier($identifier);
        if ($page !== null) {
            $action = new PageAction([$page], 'update', [
                'data' => [],
                'content' => $pageContents,
            ]);
            $action->executeAction();
        } else {
            $action = new PageAction([], 'create', [
                'data' => [
                    'identifier' => $identifier,
                    'name' => "{$this->database->getTitle()}: " . WCF::getLanguage()->get('wsdb.record.connections'),
                    'pageType' => 'system',
                    'originIsSystem' => 1,
                    'packageID' => $this->packageID,
                    'applicationPackageID' => 1,
                    'controller' => "wcf\\page\\{$this->database->identifier}_WsdbConnectionListPage",
                    'requireObjectID' => 1,
                ],
                'content' => $pageContents,
            ]);
            $action->executeAction();
        }
    }
}
