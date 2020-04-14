<?php declare(strict_types = 1);

/**
 * RequestHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           13.04.20
 */

namespace FastyBird\GatewayNode\Events;

use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Queries;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use GuzzleHttp;
use IPub\SlimRouter\Exceptions as SlimRouterExceptions;
use IPub\SlimRouter\Routing as SlimRouterRouting;
use Nette;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Router dynamic builder
 *
 * @package         FastyBird:GatewayNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class RequestHandler
{

	use Nette\SmartObject;

	/** @var Models\Routes\IRouteRepository */
	private $routeRepository;

	/** @var SlimRouterRouting\Router */
	private $router;

	public function __construct(
		Models\Routes\IRouteRepository $routeRepository,
		SlimRouterRouting\Router $router
	) {
		$this->routeRepository = $routeRepository;
		$this->router = $router;
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function __invoke(ServerRequestInterface $request): void
	{
		$findQuery = new Queries\FindRouteQuery();
		$findQuery->byMethod($request->getMethod());
		$findQuery->byPath($request->getUri()->getPath());

		$route = $this->routeRepository->findOneBy($findQuery);

		if ($route !== null) {
			try {
				// Check if route is registered
				$this->router->getNamedRoute($route->getName());

			} catch (SlimRouterExceptions\RuntimeException $ex) {
				// Register route
				$registeredRoute = $this->router->map($route->getMethod()->getValue(), $route->getPath(), [$this, 'handle']);
				$registeredRoute->setName($route->getName());
			}
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return ResponseInterface
	 */
	public function handle(
		ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): ResponseInterface {
		$findQuery = new Queries\FindRouteQuery();
		$findQuery->byMethod($request->getMethod());
		$findQuery->byPath($request->getUri()->getPath());

		$route = $this->routeRepository->findOneBy($findQuery);

		if ($route) {
			$client = new GuzzleHttp\Client();

			$response = $client->request(
				$route->getMethod()->getValue(),
				$route->getPath(),
				[
					'query' => $request->getQueryParams(),
					'body'  => $request->getBody()->getContents(),
				]
			);
		}

		return $response;
	}

}
