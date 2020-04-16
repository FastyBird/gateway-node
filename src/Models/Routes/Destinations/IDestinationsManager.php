<?php declare(strict_types = 1);

/**
 * IRoutesManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           16.04.20
 */

namespace FastyBird\GatewayNode\Models\Routes\Destinations;

use FastyBird\GatewayNode\Entities;
use Nette\Utils;

/**
 * Destinations entities manager interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDestinationsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\Destinations\IDestination
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Routes\Destinations\IDestination;

	/**
	 * @param Entities\Routes\Destinations\IDestination $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\Destinations\IDestination
	 */
	public function update(
		Entities\Routes\Destinations\IDestination $entity,
		Utils\ArrayHash $values
	): Entities\Routes\Destinations\IDestination;

	/**
	 * @param Entities\Routes\Destinations\IDestination $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Routes\Destinations\IDestination $entity
	): bool;

}
