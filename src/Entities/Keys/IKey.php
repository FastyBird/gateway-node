<?php declare(strict_types = 1);

/**
 * IKey.php
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

namespace FastyBird\GatewayNode\Entities\Keys;

use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Types;
use IPub\DoctrineTimestampable;

/**
 * API access key entity interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IKey extends Entities\IEntity,
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
	 * @param string $key
	 *
	 * @return void
	 */
	public function setKey(string $key): void;

	/**
	 * @return string
	 */
	public function getKey(): string;

	/**
	 * @param Types\KeyStateType $state
	 *
	 * @return void
	 */
	public function setState(Types\KeyStateType $state): void;

	/**
	 * @return Types\KeyStateType
	 */
	public function getState(): Types\KeyStateType;

}
