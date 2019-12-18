<?php

/**
 * Configuration file for DI container.
 */
return [
    // Services to add to the container.
    "services" => [
        "fail" => [
            "shared" => true,
            "active" => true,
            // Missing callback, should throw error
            /*"callback" => function () {
                return new stdClass();
            },*/
        ],
    ],
];
