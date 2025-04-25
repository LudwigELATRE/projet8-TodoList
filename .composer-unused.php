<?php


use ComposerUnused\ComposerUnused\Configuration\Configuration;

return static function (Configuration $configuration): Configuration {
    return $configuration
        ->ignorePackages([
            'doctrine/doctrine-migrations-bundle',
            'phpstan/phpdoc-parser',
            'symfony/asset',
            'symfony/dotenv',
            'symfony/expression-language',
            'symfony/flex',
            'symfony/http-client',
            'symfony/intl',
            'symfony/mime',
            'symfony/monolog-bundle',
            'symfony/process',
            'symfony/runtime',
            'symfony/serializer',
            'symfony/twig-bundle',
            'symfony/web-link',
            'symfony/yaml',
            'twig/extra-bundle',
            'twig/twig',
        ]);
};
