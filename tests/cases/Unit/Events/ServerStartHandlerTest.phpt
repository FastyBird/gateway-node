<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\ORM;
use FastyBird\GatewayNode\Events;
use FastyBird\GatewayNode\Models;
use IPub\SlimRouter\Routing as SlimRouterRouting;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class ServerStartHandlerTest extends BaseMockeryTestCase
{

	public function testServerStart(): void
	{
		$connection = Mockery::mock(DBAL\Connection::class);
		$connection
			->shouldReceive('ping')
			->withNoArgs()
			->andReturn(true)
			->times(1);

		$manager = Mockery::mock(ORM\EntityManagerInterface::class);
		$manager
			->shouldReceive('getConnection')
			->withNoArgs()
			->andReturn($connection)
			->times(1);

		$managerRegistry = Mockery::mock(Common\Persistence\ManagerRegistry::class);
		$managerRegistry
			->shouldReceive('getManager')
			->withNoArgs()
			->andReturn($manager)
			->times(1);

		$routeRepository = Mockery::mock(Models\Routes\IRouteRepository::class);
		$routeRepository
			->shouldReceive('findAllBy')
			->andReturn([])
			->times(1);

		$router = Mockery::mock(SlimRouterRouting\IRouter::class);

		$translator = Mockery::mock(Translation\Translator::class);

		$subscriber = new Events\ServerStartHandler(
			$routeRepository,
			$router,
			$translator,
			$managerRegistry
		);

		$subscriber->__invoke();

		Assert::true(true);
	}

}

$test_case = new ServerStartHandlerTest();
$test_case->run();