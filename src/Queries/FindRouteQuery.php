<?php declare(strict_types = 1);

/**
 * FindRouteQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Queries
 * @since          0.1.3
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Exceptions;
use FastyBird\GatewayNode\Types;
use IPub\DoctrineOrmQuery;

/**
 * Find routes entities query
 *
 * @package          FastyBird:GatewayNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Routes\Route
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindRouteQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private array $filter = [];

	/** @var Closure[] */
	private array $select = [];

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function byPath(string $path): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($path): void {
			$qb->andWhere('r.path = :path')->setParameter('path', $path);
		};
	}

	/**
	 * @param string $method
	 *
	 * @return void
	 */
	public function byMethod(string $method): void
	{
		if (!Types\RequestMethodType::isValidValue($method)) {
			throw new Exceptions\InvalidArgumentException('Provided value is not valid');
		}

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($method): void {
			$qb->andWhere('r.method = :method')->setParameter('method', $method);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Routes\Route> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Routes\Route> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(r.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Routes\Route> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('r');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
