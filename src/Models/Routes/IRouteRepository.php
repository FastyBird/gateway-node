<?php declare(strict_types = 1);

/**
 * IRouteRepository.php
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
use FastyBird\GatewayNode\Queries;

/**
 * Route repository interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRouteRepository
{

	/**
	 * @param Queries\FindRouteQuery $queryObject
	 *
	 * @return Entities\Routes\IRoute|null
	 *
	 * @phpstan-template T of Entities\Routes\Route
	 * @phpstan-param    Queries\FindRouteQuery<T> $queryObject
	 */
	public function findOneBy(
		Queries\FindRouteQuery $queryObject
	): ?Entities\Routes\IRoute;

}
