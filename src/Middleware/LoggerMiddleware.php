<?php declare(strict_types = 1);

/**
 * LoggerMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log;

/**
 * Requests log middleware
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class LoggerMiddleware implements MiddlewareInterface
{

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(Log\LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var ResponseInterface $response */
		$response = $handler->handle($request);

		$uri = $request->getUri()->getPath();

		$statusCode = $response->getStatusCode();

		switch ($statusCode) {
			case 500:
				$this->logger->critical('[MIDDLEWARE] Oops!!! the server got 500 error', [
					'ip'     => $request->getAttribute('ip_address'),
					'uri'    => $uri,
					'status' => $statusCode,
				]);
				break;

			case 404:
				$this->logger->warning('[MIDDLEWARE] Someone calling un-existing API action', [
					'ip'     => $request->getAttribute('ip_address'),
					'method' => $request->getMethod(),
					'uri'    => $uri,
					'status' => $statusCode,
				]);
				break;

			case 401:
				$this->logger->warning('[MIDDLEWARE] Someone calling API action without access', [
					'ip'     => $request->getAttribute('ip_address'),
					'uri'    => $uri,
					'status' => $statusCode,
				]);
				break;

			default:
				$this->logger->info('[MIDDLEWARE] Someone calling existing API action', [
					'ip'     => $request->getAttribute('ip_address'),
					'uri'    => $uri,
					'status' => $statusCode,
				]);
				break;
		}

		return $response;
	}

}
