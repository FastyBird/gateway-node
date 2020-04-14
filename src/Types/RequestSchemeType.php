<?php declare(strict_types = 1);

/**
 * RequestSchemeType.php
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
 * Request scheme types
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class RequestSchemeType extends Consistence\Enum\Enum
{

	/**
	 * Define scheme types
	 */
	public const METHOD_HTTP = 'http';
	public const METHOD_HTTPS = 'https';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
