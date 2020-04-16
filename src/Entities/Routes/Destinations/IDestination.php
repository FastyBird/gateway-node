<?php declare(strict_types = 1);

/**
 * Route.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Entities\Routes\Destinations;

use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Types;
use IPub\DoctrineTimestampable;

/**
 * Route destination entity interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDestination extends Entities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param Types\RequestMethodType $method
	 *
	 * @return void
	 */
	public function setMethod(Types\RequestMethodType $method): void;

	/**
	 * @return Types\RequestMethodType
	 */
	public function getMethod(): Types\RequestMethodType;

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function setPath(string $path): void;

	/**
	 * @return string
	 */
	public function getPath(): string;

	/**
	 * @param string[] $headers
	 *
	 * @return void
	 */
	public function setHeaders(array $headers): void;

	/**
	 * @param string $header
	 *
	 * @return void
	 */
	public function addHeader(string $header): void;

	/**
	 * @return string[]
	 */
	public function getHeaders(): array;

	/**
	 * @param Entities\Routes\Nodes\INode $node
	 *
	 * @return void
	 */
	public function setNode(Entities\Routes\Nodes\INode $node): void;

	/**
	 * @return Entities\Routes\Nodes\INode
	 */
	public function getNode(): Entities\Routes\Nodes\INode;

	/**
	 * @param Entities\Routes\IRoute $route
	 *
	 * @return void
	 */
	public function setRoute(Entities\Routes\IRoute $route): void;

	/**
	 * @return Entities\Routes\IRoute
	 */
	public function getRoute(): Entities\Routes\IRoute;

}
