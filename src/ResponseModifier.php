<?php
declare(strict_types=1);

namespace MixerApi\HalView;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * CakePHP does not have a mime type for hal+json, this class adds hal+json to Cake\Http\Response::_mimeTypes
 *
 * @todo when cakephp adds mime types add a version check to determine whether to add the types or not
 * @link https://github.com/cakephp/cakephp/issues/14796
 */
class ResponseModifier
{
    /**
     * HAL+JSON
     *
     * @var string
     */
    private const TYPE = 'hal+json';

    /**
     * Hypertext Application Language MIME type mapping
     *
     * @var string
     */
    private const MIME_TYPE_MAP = [
        'application/hal+json',
        'application/vnd.hal+json',
    ];

    /**
     * Registers Controller.startup listener so HAL can be added to Cake\Http\Response
     *
     * @return void
     */
    public function listen(): void
    {
        EventManager::instance()
            ->on('Controller.startup', function (Event $event) {

                $response = $this->modify(
                    $event->getSubject()->getRequest(),
                    $event->getSubject()->getResponse()
                );

                $event->getSubject()->setResponse($response);
            });
    }

    /**
     * Appends HAL MIME types to the Response if the ServerRequest accepts HAL
     *
     * @param \Cake\Http\ServerRequest $request cake ServerRequest
     * @param \Cake\Http\Response $response cake Response
     * @return \Cake\Http\Response
     */
    public function modify(ServerRequest $request, Response $response): Response
    {
        if (!$this->isHal($request)) {
            return $response;
        }

        $response->setTypeMap(self::TYPE, self::MIME_TYPE_MAP);

        return $response;
    }

    /**
     * Is this a HAL request?
     *
     * @param \Cake\Http\ServerRequest $request cake ServerRequest
     * @return bool
     */
    private function isHal(ServerRequest $request): bool
    {
        foreach (self::MIME_TYPE_MAP as $type) {
            if ($request->accepts($type)) {
                return true;
            }
        }

        return false;
    }
}
