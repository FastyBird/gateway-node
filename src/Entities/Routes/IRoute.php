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

namespace FastyBird\GatewayNode\Entities\Routes;

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
interface IRoute extends Entities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name): void;

	/**
	 * @return string
	 */
	public function getName(): string;

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
	 * @param string $destination
	 *
	 * @return void
	 */
	public function setDestination(string $destination): void;

	/**
	 * @return string
	 */
	public function getDestination(): string;

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

}
