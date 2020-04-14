<?php declare(strict_types = 1);

/**
 * INode.php
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

namespace FastyBird\GatewayNode\Entities\Routes\Nodes;

use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Types;
use IPub\DoctrineTimestampable;

/**
 * Route entity interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface INode extends Entities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param Types\RequestSchemeType $scheme
	 *
	 * @return void
	 */
	public function setScheme(Types\RequestSchemeType $scheme): void;

	/**
	 * @return Types\RequestSchemeType
	 */
	public function getScheme(): Types\RequestSchemeType;

	/**
	 * @param string $host
	 *
	 * @return void
	 */
	public function setHost(string $host): void;

	/**
	 * @return string
	 */
	public function getHost(): string;

	/**
	 * @param int $port
	 *
	 * @return void
	 */
	public function setPort(int $port): void;

	/**
	 * @return int
	 */
	public function getPort(): int;

	/**
	 * @param Entities\Routes\IRoute[] $routes
	 */
	public function setRoutes(array $routes = []): void;

	/**
	 * @param Entities\Routes\IRoute $route
	 *
	 * @return void
	 */
	public function addRoute(Entities\Routes\IRoute $route): void;

	/**
	 * @return Entities\Routes\IRoute[]
	 */
	public function getRoutes(): array;

	/**
	 * @param Entities\Routes\IRoute $route
	 *
	 * @return void
	 */
	public function removeRoute(Entities\Routes\IRoute $route): void;

}
