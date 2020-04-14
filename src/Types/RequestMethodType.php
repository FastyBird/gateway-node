<?php declare(strict_types = 1);

/**
 * RequestMethodType.php
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
use Fig\Http\Message\RequestMethodInterface;

/**
 * Request method types
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class RequestMethodType extends Consistence\Enum\Enum
{

	/**
	 * Define data types
	 */
	public const METHOD_GET = RequestMethodInterface::METHOD_GET;
	public const METHOD_POST = RequestMethodInterface::METHOD_POST;
	public const METHOD_PATCH = RequestMethodInterface::METHOD_PATCH;
	public const METHOD_PUT = RequestMethodInterface::METHOD_PUT;
	public const METHOD_DELETE = RequestMethodInterface::METHOD_DELETE;
	public const METHOD_OPTIONS = RequestMethodInterface::METHOD_OPTIONS;

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
