<?php declare(strict_types = 1);

/**
 * RouteRepository.php
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

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Queries;
use Nette;

/**
 * Route node repository
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class NodeRepository implements INodeRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Routes\Nodes\Node>|null */
	private $repository = null;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindRouteNodeQuery $queryObject
	): ?Entities\Routes\Nodes\INode {
		/** @var Entities\Routes\Nodes\INode|null $property */
		$property = $queryObject->fetchOne($this->getRepository());

		return $property;
	}

	/**
	 * @return ORM\EntityRepository<Entities\Routes\Nodes\Node>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Routes\Nodes\Node::class);
		}

		return $this->repository;
	}

}
