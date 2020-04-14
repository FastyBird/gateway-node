<?php declare(strict_types = 1);

/**
 * GatewayV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           04.04.20
 */

namespace FastyBird\GatewayNode\Controllers;

use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Nette;
use Psr\Http\Message;

/**
 * Gateway controller
 *
 * @package         FastyBird:GatewayNode!
 * @subpackage      Controllers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class GatewayV1Controller
{

	use Nette\SmartObject;

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 */
	public function handle(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// TODO: pass call to node
		return $response;
	}

}
