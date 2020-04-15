<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\GatewayNode\Commands;
use FastyBird\GatewayNode\Events;
use FastyBird\GatewayNode\Middleware;
use FastyBird\GatewayNode\Models;
use FastyBird\NodeLibs\Boot;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$configurator = Boot\Bootstrap::boot();
		$configurator->addParameters([
			'database' => [
				'driver' => 'pdo_sqlite',
			],
		]);

		$container = $configurator->createContainer();

		Assert::notNull($container->getByType(Middleware\ApiKeyValidatorMiddleware::class));
		Assert::notNull($container->getByType(Middleware\DbErrorMiddleware::class));
		Assert::notNull($container->getByType(Middleware\CorsMiddleware::class));
		Assert::notNull($container->getByType(Middleware\LoggerMiddleware::class));

		Assert::notNull($container->getByType(Events\ServerStartHandler::class));
		Assert::notNull($container->getByType(Events\RequestHandler::class));
		Assert::notNull($container->getByType(Events\ResponseHandler::class));
		Assert::notNull($container->getByType(Events\AfterConsumeHandler::class));

		Assert::notNull($container->getByType(Commands\Keys\CreateCommand::class));
		Assert::notNull($container->getByType(Commands\Routes\CreateCommand::class));

		Assert::notNull($container->getByType(Models\Keys\KeyRepository::class));
		Assert::notNull($container->getByType(Models\Routes\RouteRepository::class));
		Assert::notNull($container->getByType(Models\Routes\Nodes\NodeRepository::class));

		Assert::notNull($container->getByType(Models\Keys\KeysManager::class));
		Assert::notNull($container->getByType(Models\Routes\RoutesManager::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
