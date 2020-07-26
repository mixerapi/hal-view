<?php
declare(strict_types=1);

namespace MixerApi\HalView;

use Cake\Datasource\EntityInterface;

/**
 * This class checks a Cake\ORM\Entity for properties that can be embedded into a HAL+JSON response and returns a
 * key-value pair. The key is the property name and the value is the data contained by that property. The following
 * fields are not embeddable into a HAL resource:
 *
 * - That begin with an underscore "_"
 * - Values that are not one of: array or EntityInterface
 */
class EmbeddableHalResource
{
    /**
     * Key-value pair of embeddable resources
     *
     * @var array
     */
    private $embeddable = [];

    /**
     * @param \Cake\Datasource\EntityInterface $entity Cake\ORM\Entity or an EntityInterface
     */
    public function __construct(EntityInterface $entity)
    {
        $properties = array_filter($entity->getVisible(), function ($property) use ($entity) {

            if (!isset($entity->{$property}) || strpos($property, '_') === 0) {
                return false;
            }

            if (!is_array($entity->{$property}) && !$entity->{$property} instanceof EntityInterface) {
                return false;
            }

            return true;
        });

        foreach ($properties as $property) {
            $this->embeddable[$property] = $entity->{$property};
        }
    }

    /**
     * Returns a mixed array as a key-value pair of data that can be embedded. The key is the Entities property
     * name, the value is the mixed value.
     *
     * @return array
     */
    public function getEmbeddable(): array
    {
        return $this->embeddable;
    }
}
