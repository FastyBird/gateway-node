<?php declare(strict_types = 1);

/**
 * IKeyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Models\Keys;

use FastyBird\GatewayNode\Entities;

/**
 * Key repository interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IKeyRepository
{

	/**
	 * @param string $identifier
	 *
	 * @return Entities\Keys\IKey|null
	 */
	public function findOneByIdentifier(string $identifier): ?Entities\Keys\IKey;

	/**
	 * @param string $key
	 *
	 * @return Entities\Keys\IKey|null
	 */
	public function findOneByKey(string $key): ?Entities\Keys\IKey;

}
