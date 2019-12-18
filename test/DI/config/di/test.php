<?php

/**
 * Configuration file for DI container.
 */
return [
    // Services to add to the container.
    "services" => [
        "test1" => [
            "callback" => function () {
                return new stdClass();
            },
        ],
        "test2" => [
            // callback is missing
            "shared" => true,
            "callback" => function () {
                return new stdClass();
            },
        ],
        "test3" => [
            "active" => true,
            "callback" => function () {
                return new stdClass();
            },
        ],
    ],
];
