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

namespace FastyBird\GatewayNode\Models\Routes;

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Queries;
use Nette;
use Throwable;

/**
 * Route repository
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RouteRepository implements IRouteRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Persistence\ObjectRepository<Entities\Routes\Route>|null */
	private $repository = null;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindRouteQuery $queryObject
	): ?Entities\Routes\IRoute {
		/** @var Entities\Routes\IRoute|null $property */
		$property = $queryObject->fetchOne($this->getRepository());

		return $property;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function findAllBy(
		Queries\FindRouteQuery $queryObject
	): array {
		$result = $queryObject->fetch($this->getRepository());

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * @return Persistence\ObjectRepository<Entities\Routes\Route>
	 */
	private function getRepository(): Persistence\ObjectRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Routes\Route::class);
		}

		return $this->repository;
	}

}
