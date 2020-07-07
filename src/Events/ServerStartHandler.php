<?php declare(strict_types = 1);

/**
 * ServerStartHandler.php
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
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Queries;
use FastyBird\NodeJsonApi\Exceptions  as NodeJsonApiExceptions;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
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

	/** @var SlimRouterRouting\IRouter */
	private $router;

	/** @var Translation\Translator */
	private $translator;

	public function __construct(
		Models\Routes\IRouteRepository $routeRepository,
		SlimRouterRouting\IRouter $router,
		Translation\Translator $translator
	) {
		$this->routeRepository = $routeRepository;
		$this->router = $router;
		$this->translator = $translator;
	}

	/**
	 * @return void
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function __invoke(): void
	{
		$findQuery = new Queries\FindRouteQuery();

		$routes = $this->routeRepository->findAllBy($findQuery);

		foreach ($routes as $route) {
			// Register route
			$this->router->map([$route->getMethod()->getValue()], $route->getPath(), function (
				ServerRequestInterface $request
			) use ($route): ResponseInterface {
				$client = new GuzzleHttp\Client();

				$lastError = null;

				$requestBody = $request->getBody()->getContents();

				foreach ($route->getDestinations() as $destination) {
					try {
						return $client->request(
							$destination->getMethod()->getValue(),
							$this->buildDestination($request, $destination),
							[
								GuzzleHttp\RequestOptions::QUERY   => $request->getQueryParams(),
								GuzzleHttp\RequestOptions::BODY    => $requestBody,
								GuzzleHttp\RequestOptions::HEADERS => $this->buildHeaders($request, $destination),
							]
						);

					} catch (GuzzleHttp\Exception\BadResponseException $ex) {
						if ($ex->getResponse() !== null) {
							$lastError = $ex->getResponse();
						}

					} catch (GuzzleHttp\Exception\ConnectException $ex) {
						// Connection to microservice failed
					}
				}

				if ($lastError !== null) {
					return $lastError;
				}

				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
					$this->translator->translate('//node.base.messages.serverError.heading'),
					$this->translator->translate('//node.base.messages.serverError.message')
				);
			});
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param Entities\Routes\Destinations\IDestination $destination
	 *
	 * @return string
	 */
	private function buildDestination(
		ServerRequestInterface $request,
		Entities\Routes\Destinations\IDestination $destination
	): string {
		$path = ltrim($destination->getPath(), '/');

		foreach ($request->getAttributes() as $key => $value) {
			if (!Utils\Strings::startsWith($key, '__')) {
				$path = str_replace('{' . $key . '}', $value, $path);
			}
		}

		$uri = new GuzzleHttp\Psr7\Uri();
		$uri = $uri->withScheme($destination->getNode()->getScheme()->getValue());
		$uri = $uri->withHost($destination->getNode()->getHost());
		$uri = $uri->withPort($destination->getNode()->getPort());
		$uri = $uri->withPath($path);

		return (string) $uri;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param Entities\Routes\Destinations\IDestination $destination
	 *
	 * @return mixed[]
	 */
	private function buildHeaders(
		ServerRequestInterface $request,
		Entities\Routes\Destinations\IDestination $destination
	): array {
		$headers = [];

		foreach ($request->getHeaders() as $name => $value) {
			$headers[strtolower($name)] = reset($value);
		}

		$passHeaders = [];

		foreach ($destination->getHeaders() as $header) {
			if (array_key_exists($header, $headers)) {
				$passHeaders[$header] = $headers[$header];
			}
		}

		return $passHeaders;
	}

}
