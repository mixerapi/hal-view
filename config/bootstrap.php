<?php
use Cake\Http\ServerRequest;

ServerRequest::addDetector(
    'hal',
    [
        'accept' => ['application/hal+json'],
        'param' => '_ext',
        'value' => 'hal+json',
    ]
);