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

use FastyBird\GatewayNode\Controllers;
use IPub\SlimRouter\Exceptions as SlimRouterExceptions;
use IPub\SlimRouter\Routing as SlimRouterRouting;
use Nette;
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

	/** @var SlimRouterRouting\Router */
	private $router;

	/** @var Controllers\GatewayV1Controller */
	private $controller;

	public function __construct(
		SlimRouterRouting\Router $router,
		Controllers\GatewayV1Controller $controller
	) {
		$this->router = $router;
		$this->controller = $controller;
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function __invoke(ServerRequestInterface $request): void
	{
		try {
			// Check if route is registered
			$this->router->getNamedRoute('route-name');

		} catch (SlimRouterExceptions\RuntimeException $ex) {
			// Register route
			$route = $this->router->get('loaded-path', [$this->controller, 'handle']);
			$route->setName('route-name');
		}
	}

}
