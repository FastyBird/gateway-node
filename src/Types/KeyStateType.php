<?php declare(strict_types = 1);

/**
 * KeyStateType.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Types
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Types;

use Consistence;

/**
 * API access key state column
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class KeyStateType extends Consistence\Enum\Enum
{

	/**
	 * Define states
	 */
	public const STATE_ACTIVE = 'active';
	public const STATE_SUSPENDED = 'suspended';
	public const STATE_DELETED = 'deleted';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
