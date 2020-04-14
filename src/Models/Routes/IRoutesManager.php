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
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Models\Routes;

use FastyBird\GatewayNode\Entities;
use Nette\Utils;

/**
 * Routes entities manager interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRoutesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\IRoute
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Routes\IRoute;

	/**
	 * @param Entities\Routes\IRoute $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\IRoute
	 */
	public function update(
		Entities\Routes\IRoute $entity,
		Utils\ArrayHash $values
	): Entities\Routes\IRoute;

	/**
	 * @param Entities\Routes\IRoute $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Routes\IRoute $entity
	): bool;

}
