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
 * @date           15.04.20
 */

namespace FastyBird\GatewayNode\Events;

use Contributte\Translation;
use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\ORM;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Queries;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp;
use IPub\SlimRouter\Routing as SlimRouterRouting;
use Nette;
use Nette\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Http server start handler
 *
 * @package         FastyBird:GatewayNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ServerStartHandler
{

	use Nette\SmartObject;

	/** @var Models\Routes\IRouteRepository */
	private $routeRepository;

	/** @var SlimRouterRouting\Router */
	private $router;

	/** @var Translation\Translator */
	private $translator;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Routes\IRouteRepository $routeRepository,
		SlimRouterRouting\Router $router,
		Translation\Translator $translator,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->routeRepository = $routeRepository;
		$this->router = $router;
		$this->translator = $translator;
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * @return void
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function __invoke(): void
	{
		try {
			$em = $this->managerRegistry->getManager();

			if ($em instanceof ORM\EntityManagerInterface) {
				$em->getConnection()->ping();
			}

		} catch (DBAL\DBALException $ex) {
			throw new NodeLibsExceptions\TerminateException('Database error: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		$findQuery = new Queries\FindRouteQuery();

		$routes = $this->routeRepository->findAllBy($findQuery);

		foreach ($routes as $route) {
			// Register route
			$this->router->map([$route->getMethod()->getValue()], $route->getPath(), function (
				ServerRequestInterface $request,
				NodeWebServerHttp\Response $response
			) use ($route): ResponseInterface {
				$client = new GuzzleHttp\Client();

				try {
					return $client->request(
						$route->getMethod()->getValue(),
						$this->buildDestination($request, $route),
						[
							'query' => $request->getQueryParams(),
							'body'  => $request->getBody()->getContents(),
						]
					);

				} catch (GuzzleHttp\Exception\BadResponseException $ex) {
					if ($ex->getResponse() !== null) {
						return $ex->getResponse();
					}
				}

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
					$this->translator->translate('//node.base.messages.serverError.heading'),
					$this->translator->translate('//node.base.messages.serverError.message')
				);
			});
		}
	}

	/**
	 * @param Entities\Routes\IRoute $route
	 * @param ServerRequestInterface $request
	 *
	 * @return string
	 */
	private function buildDestination(
		ServerRequestInterface $request,
		Entities\Routes\IRoute $route
	): string {
		$destination = ltrim($route->getDestination(), '/');

		foreach ($request->getAttributes() as $key => $value) {
			if (!Utils\Strings::startsWith($key, '__')) {
				$destination = str_replace('{' . $key . '}', $value, $destination);
			}
		}

		$uri = new GuzzleHttp\Psr7\Uri();
		$uri = $uri->withScheme($route->getNode()->getScheme()->getValue());
		$uri = $uri->withHost($route->getNode()->getHost());
		$uri = $uri->withPort($route->getNode()->getPort());
		$uri = $uri->withPath($destination);

		return (string) $uri;
	}

}