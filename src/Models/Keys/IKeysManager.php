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
use FastyBird\GatewayNode\Models;
use Nette\Utils;

/**
 * Keys entities manager interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IKeysManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Keys\IKey
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Keys\IKey;

	/**
	 * @param Entities\Keys\IKey $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Keys\IKey
	 */
	public function update(
		Entities\Keys\IKey $entity,
		Utils\ArrayHash $values
	): Entities\Keys\IKey;

	/**
	 * @param Entities\Keys\IKey $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Keys\IKey $entity
	): bool;

}
