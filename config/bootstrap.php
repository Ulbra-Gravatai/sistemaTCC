<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . "/../vendor/autoload.php";

$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/orm/"), $isDevMode);

$conn = [
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'dbname'   => 'sistema_tcc',
    'user'     => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];

$db = __DIR__ . "/db.yml";

if (file_exists($db)) {
   $db = Yaml::parse(file_get_contents($db));
   $conn = array_merge($conn, $db);
}


$entityManager = EntityManager::create($conn, $config);
