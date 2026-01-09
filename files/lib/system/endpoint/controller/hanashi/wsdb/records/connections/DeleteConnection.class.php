<?php

namespace wcf\system\endpoint\controller\hanashi\wsdb\records\connections;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\wsdb\record\connection\RecordConnection;
use wcf\data\wsdb\record\connection\RecordConnectionAction;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;

#[DeleteRequest("/hanashi/wsdb/records/connections/{id:\\d+}")]
final class DeleteConnection implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $connection = Helper::fetchObjectFromRequestParameter($variables['id'], RecordConnection::class);

        if (!$connection->getRecord()->canEdit()) {
            throw new PermissionDeniedException();
        }

        (new RecordConnectionAction([$connection], 'delete'))->executeAction();

        return new JsonResponse([]);
    }
}
