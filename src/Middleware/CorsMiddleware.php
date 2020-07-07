<?php declare(strict_types = 1);

/**
 * CorsMiddleware.php
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

use Contributte\Translation;
use FastyBird\NodeJsonApi\Exceptions  as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Neomerx\Cors\Analyzer as CorsAnalyzer;
use Neomerx\Cors\Contracts\AnalysisResultInterface as CorsAnalysisResultInterface;
use Neomerx\Cors\Contracts\Constants\CorsResponseHeaders;
use Neomerx\Cors\Strategies\Settings as CorsSettings;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * API key check middleware
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CorsMiddleware implements MiddlewareInterface
{

	/** @var mixed[] */
	private $options = [
		'origin'          => '*',
		'methods'         => [
			RequestMethodInterface::METHOD_GET,
			RequestMethodInterface::METHOD_POST,
			RequestMethodInterface::METHOD_PUT,
			RequestMethodInterface::METHOD_PATCH,
			RequestMethodInterface::METHOD_DELETE,
		],
		'headers.allow'   => [],
		'headers.expose'  => [],
		'credentials'     => false,
		'origin.protocol' => 'http',
		'origin.server'   => 'localhost',
		'origin.port'     => 80,
		'cache'           => 0,
	];

	/** @var Translation\Translator */
	private $translator;

	/** @var NodeWebServerHttp\ResponseFactory */
	private $responseFactory;

	/** @var LoggerInterface */
	private $logger;

	/**
	 * @param mixed[] $options
	 * @param Translation\Translator $translator
	 * @param NodeWebServerHttp\ResponseFactory $responseFactory
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		array $options,
		Translation\Translator $translator,
		NodeWebServerHttp\ResponseFactory $responseFactory,
		LoggerInterface $logger
	) {
		$this->translator = $translator;
		$this->responseFactory = $responseFactory;
		$this->logger = $logger;

		foreach ($options as $key => $value) {
			$this->options[$key] = $value;
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 * @throws Translation\Exceptions\InvalidArgument
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$analyzer = CorsAnalyzer::instance($this->buildSettings($request));
		$analyzer->setLogger($this->logger);

		$cors = $analyzer->analyze($request);

		switch ($cors->getRequestType()) {
			case CorsAnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNAUTHORIZED,
					$this->translator->translate('//node.base.messages.notAllowed.heading'),
					'CORS request origin is not allowed.'
				);

			case CorsAnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNAUTHORIZED,
					$this->translator->translate('//node.base.messages.notAllowed.heading'),
					'CORS requested method is not supported.'
				);

			case CorsAnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNAUTHORIZED,
					$this->translator->translate('//node.base.messages.notAllowed.heading'),
					'CORS requested header is not allowed.'
				);

			case CorsAnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
				$headers = $cors->getResponseHeaders();

				$response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_OK);

				foreach ($headers as $header => $value) {
					if (is_array($value) === false) {
						$value = (string) $value;
					}

					$response->withHeader($header, $value);
				}

				return $response;

			case CorsAnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
				return $handler->handle($request);

			default:
				/** @var ResponseInterface $response */
				$response = $handler->handle($request);

				$headers = $cors->getResponseHeaders();
				$headers = $this->fixHeaders($headers);

				foreach ($headers as $header => $value) {
					if (is_array($value) === false) {
						$value = (string) $value;
					}

					$response->withHeader($header, $value);
				}

				return $response;
		}
	}

	/**
	 * Build a CORS settings object
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return CorsSettings
	 */
	private function buildSettings(
		ServerRequestInterface $request
	): CorsSettings {
		$settings = new CorsSettings();

		if (is_string($this->options['origin.server'])) {
			$settings->setServerOrigin(
				$this->options['origin.protocol'],
				$this->options['origin.server'],
				$this->options['origin.port']
			);
		}

		$settings->setPreFlightCacheMaxAge((int) $this->options['cache']);

		if ($this->options['origin'] === '*') {
			$settings->enableAllOriginsAllowed();

		} else {
			$settings->setAllowedOrigins((array) $this->options['origin']);
		}

		$settings->setAllowedMethods((array) $this->options['methods']);
		$settings->setAllowedHeaders((array) $this->options['headers.allow']);
		$settings->setExposedHeaders((array) $this->options['headers.expose']);

		if ($this->options['credentials']) {
			$settings->setCredentialsSupported();

		} else {
			$settings->setCredentialsNotSupported();
		}

		$settings->enableCheckHost();

		return $settings;
	}

	/**
	 * Edge cannot handle multiple Access-Control-Expose-Headers headers
	 *
	 * @param mixed[] $headers
	 *
	 * @return mixed[]
	 */
	private function fixHeaders(array $headers): array
	{
		if (isset($headers[CorsResponseHeaders::EXPOSE_HEADERS])) {
			$headers[CorsResponseHeaders::EXPOSE_HEADERS] = implode(',', $headers[CorsResponseHeaders::EXPOSE_HEADERS]);
		}

		return $headers;
	}

}
