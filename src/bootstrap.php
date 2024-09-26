<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$paths = [__DIR__ . '/Models'];
$isDevMode = true;

$dbParams = $config['db'];

$config = Setup::createAnnotationMetadataConfiguration(
    $paths,  // Use the $paths variable here
    $isDevMode,
    null,
    null,
    false  // This disables the SimpleAnnotationReader
);

$entityManager = EntityManager::create($dbParams, $config);

return $entityManager;