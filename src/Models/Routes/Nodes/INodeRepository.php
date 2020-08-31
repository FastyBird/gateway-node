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

namespace FastyBird\GatewayNode\Models\Routes\Nodes;

use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Queries;

/**
 * Route node repository interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface INodeRepository
{

	/**
	 * @param Queries\FindRouteNodeQuery $queryObject
	 *
	 * @return Entities\Routes\Nodes\INode|null
	 *
	 * @phpstan-template T of Entities\Routes\Nodes\Node
	 * @phpstan-param    Queries\FindRouteNodeQuery<T> $queryObject
	 */
	public function findOneBy(
		Queries\FindRouteNodeQuery $queryObject
	): ?Entities\Routes\Nodes\INode;

	/**
	 * @param Queries\FindRouteNodeQuery $queryObject
	 *
	 * @return Entities\Routes\Nodes\INode[]
	 *
	 * @phpstan-template T of Entities\Routes\Nodes\Node
	 * @phpstan-param    Queries\FindRouteNodeQuery<T> $queryObject
	 */
	public function findAllBy(
		Queries\FindRouteNodeQuery $queryObject
	): array;

}
