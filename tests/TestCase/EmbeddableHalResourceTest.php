<?php

namespace MixerApi\HalView\Test\TestCase;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use MixerApi\HalView\EmbeddableHalResource;

class EmbeddableHalResourceTest extends TestCase
{
    /**
     * Test that EmbeddableHalResource correct determines what is embeddable
     */
    public function testConstructWithResults()
    {
        $entity = new Entity([
            'id' => 1,
            'name' => 'Andrew',
            'friends' => [new Entity(['name' => 'Brittany'])],
            'job' => new Entity(['title' => 'Developer', 'organization' => 'Acme']),
            '_joinData' => []
        ]);

        $this->assertCount(2, (new EmbeddableHalResource($entity))->getEmbeddable());
    }

    /**
     * Test that EmbeddableHalResource does not detect anything as embeddable
     */
    public function testConstructWithoutResults()
    {
        $entity = new Entity([
            'id' => 1,
            'name' => 'Andrew',
            'created' => new FrozenTime(),
            '_joinData' => []
        ]);

        $this->assertCount(0, (new EmbeddableHalResource($entity))->getEmbeddable());
    }
}