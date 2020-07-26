# MixerApi HAL View

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mixerapi/hal-view.svg?style=flat-square)](https://packagist.org/packages/mixerapi/hal-view)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.txt)
[![Build Status](https://travis-ci.org/mixerapi/hal-view.svg?branch=master)](https://travis-ci.org/github/mixerapi/hal-view)
[![Coverage Status](https://coveralls.io/repos/github/mixerapi/hal-view/badge.svg?branch=master)](https://coveralls.io/github/mixerapi/hal-view?branch=master)

*Note, this is an alpha stage package.

A Hypertext Application Language ([HAL](http://stateless.co/hal_specification.html)) View for CakePHP. This plugin 
supports links, pagination, and embedded resources. Once setup any request with `application/hal+json` or 
will be rendered by the plugin.

## Installation

```bash
composer require mixerapi/hal-view
bin/cake plugin load MixerApi/HalView
```

Alternatively after composer installing you can manually load the plugin in your Application:

```php
# src/Application.php
public function bootstrap(): void
{
    // other logic...
    $this->addPlugin('MixerApi/HalView');
}
```

## Setup

Modify your `RequestHandler` component to support HAL views. This is typically done in your AppController:

```php
# src/Controller/AppController.php
public function initialize(): void
{
    parent::initialize();
    $this->loadComponent('RequestHandler', [
        'viewClassMap' => [
            'hal+json' => 'MixerApi/HalView.HalJson'
        ]
    ]);
    // other logic... 
}
```

## Usage

For `_link.self.href` support you will need to implement `MixerApi\HalView\HalResourceInterface` on entities that you 
want to expose as HAL resources. This informs the plugin that the Entity should be treated as a HAL resource and 
provides the mapper with a `_link.self.href` URL. Example:

```php
<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use MixerApi\HalView\HalResourceInterface;
use Cake\Datasource\EntityInterface;

class Actor extends Entity implements HalResourceInterface
{
    // your various properties and logic

    /**
     * @param EntityInterface $entity
     * @return array|\string[][]
     */
    public function getHalLinks(EntityInterface $entity): array
    {
        return [
            'self' => [
                'href' => '/actors/' . $entity->get('id')
            ]
        ];
    }
}
```

Now an HTTP GET to the `/actors/1` endpoint will render HAL using the CakePHP native serialization process:

```php
#src/Controller/ActorsController.php
public function view($id = null)
{
    $this->request->allowMethod('get');
    $actor = $this->Actors->get($id, [
        'contain' => ['Films'],
    ]);
    $this->set('actor', $actor);
    $this->viewBuilder()->setOption('serialize', 'actor');
}
```

Output:

```json
{
  "_links": {
    "self": {
      "href": "/actors/149"
    }
  },
  "id": 149,
  "first_name": "RUSSELL",
  "last_name": "TEMPLE",
  "modified": "2006-02-15T04:34:33+00:00",
  "_embedded": {
    "films": [
      {
        "id": 53,
        "title": "BANG KWAI",
        "description": "A Epic Drama of a Madman And a Cat who must Face a A Shark in An Abandoned Amusement Park",
        "release_year": "2006",
        "language_id": 1,
        "rental_duration": 5,
        "rental_rate": "2.99",
        "length": 87,
        "replacement_cost": "25.99",
        "rating": "NC-17",
        "special_features": "Commentaries,Deleted Scenes,Behind the Scenes",
        "modified": "2006-02-15T05:03:42+00:00"
        "_links": {
          "self": {
            "href": "/films/53"
          }
        }
      }
    ]
  }
}
```

If your Entity does not implement the interface it will still be returned as HAL resource when serialized, but minus 
the `_links` property. Collection requests will work without this interface as well, example:

```php
#src/Controller/ActorsController.php
public function index()
{
    $this->request->allowMethod('get');
    $actors = $this->paginate($this->Actors, [
        'contain' => ['Films'],
    ]);
    $this->set(compact('actors'));
    $this->viewBuilder()->setOption('serialize', 'actors');
}
```

Output:

```json
{
  "_links": {
    "self": {
      "href": "/actors?page=3"
    },
    "next": {
      "href": "/actors?page=4"
    },
    "prev": {
      "href": "/actors?page=2"
    },
    "first": {
      "href": "/actors?page=1"
    },
    "last": {
      "href": "/actors?page=11"
    }
  },
  "count": 20,
  "total": 207,
  "_embedded": {
    "actors": [
      {
        "id": 1,
        "first_name": "PENELOPE",
        "last_name": "GUINESS",
        "modified": "2006-02-15T04:34:33+00:00"
        "_embedded": {
          "films": [
            {
              "id": 1,
              "title": "ACADEMY DINOSAUR",
              "description": "A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies",
              "release_year": "2006",
              "language_id": 1,
              "rental_duration": 6,
              "rental_rate": "0.99",
              "length": 86,
              "replacement_cost": "20.99",
              "rating": "PG",
              "special_features": "Deleted Scenes,Behind the Scenes",
              "modified": "2006-02-15T05:03:42+00:00"
            }
          ]
        }
      }
    ]
  }
}
```

If the Actor and Film entities were implementing `MixerApi\HalView\HalResourceInterface` then the example above would 
include the `_links` property for each serialized entity.

Try it out for yourself:

```bash
# json
curl -X GET "http://localhost:8765/actors" -H "accept: application/hal+json"
```

## Serializing

Optionally, you can manually serialize data into HAL using `JsonSerializer`. This is the same class that the main HalJsonView uses. Example:

```php
use MixerApi\HalView\JsonSerializer;

# json
$json = (new JsonSerializer($data))->asJson(JSON_PRETTY_PRINT); // asJson argument is optional

# array
$hal = (new JsonSerializer($data))->getData();

# json with `_links.self.href` and pagination meta data
use Cake\Http\ServerRequest;
use Cake\View\Helper\PaginatorHelper;
$json = (new JsonSerializer($data, new ServerRequest(), new PaginatorHelper()))->asJson();
```

View the [JsonSerializer](src/JsonSerializer.php) for more details.

## Unit Tests

```bash
# unit test only
vendor/bin/phpunit

# standards checking
composer check
```
