<?php

namespace MixerApi\HalView\Test\TestCase;

use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use MixerApi\HalView\ResponseModifier;
use ReflectionFunction;

class ResponseModifierTest extends TestCase
{
    private const MIME_TYPE_MAP = [
        'application/hal+json',
        'application/vnd.hal+json'
    ];

    /**
     * Test that Hal mime types are added to Response instance when is a hal request
     */
    public function testModifyWithHal()
    {
        $request = (new ServerRequest())->withEnv('HTTP_ACCEPT', 'application/hal+json, text/plain, */*');
        $response = (new ResponseModifier())->modify($request, new Response());
        $this->assertEquals(self::MIME_TYPE_MAP, $response->getMimeType('hal+json'));
    }

    /**
     * Test that Hal mime types are NOT added to Response instance by default
     */
    public function testModifyWithoutHal()
    {
        $request = (new ServerRequest())->withEnv('HTTP_ACCEPT', 'application/json');
        $response = (new ResponseModifier())->modify($request, new Response());
        $this->assertNotEquals(self::MIME_TYPE_MAP, $response->getMimeType('hal+json'));
    }

    /**
     * Test ResponseModifer->listen()
     */
    public function testListen()
    {
        (new ResponseModifier())->listen();
        $eventManager = EventManager::instance();
        $listeners = $eventManager->matchingListeners('Controller.startup');
        $names = [];
        foreach ($listeners['Controller.startup'] as $listens) {
            foreach ($listens as $listen) {
                $names[] = (new ReflectionFunction($listen['callable']))->name;
            }
        }

        $results = array_filter($names, function($name) {
            return strstr($name, 'MixerApi\HalView\{closure}');
        });

        $this->assertNotEmpty($results);
    }
}