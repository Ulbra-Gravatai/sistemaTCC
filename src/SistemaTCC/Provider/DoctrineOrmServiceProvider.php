<?php

namespace SistemaTCC\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

class DoctrineOrmServiceProvider implements ServiceProviderInterface {

    public function register(Container $app) {

        $app['orm'] = function ($app) {

            $isDevMode = true;
            $config = Setup::createYAMLMetadataConfiguration([(__DIR__ . "/../../../config/orm/")], $isDevMode);

            $conn = [
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'dbname'   => 'sistema_tcc',
                'user'     => 'root',
                'password' => '',
                'charset'  => 'utf8mb4',
            ];

			/*

				Crie o arquivo em config/db.yml tal e qual o padrão abaixo, só
				substituindo pelos seus valores correspondentes:

				driver: pdo_mysql
				host: localhost
				dbname: sistema_tcc
				user: NOME_DO_USUARIO
				password: SENHA_DO_BANCO
				charset: utf8mb4

			 */
			$db = __DIR__ . "/../../../config/db.yml";

			if (file_exists($db)) {
				$db = Yaml::parse(file_get_contents($db));
				$conn = array_merge($conn, $db);
			}

            $em = EntityManager::create($conn, $config);

            return $em;
        };

    }

}
