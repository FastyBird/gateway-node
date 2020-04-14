<?php declare(strict_types = 1);

/**
 * FindRouteNodeQuery.php
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
 * Find routes nodes entities query
 *
 * @package          FastyBird:GatewayNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Routes\Nodes\Node
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindRouteNodeQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param string $scheme
	 *
	 * @return void
	 */
	public function byScheme(string $scheme): void
	{
		if (!Types\RequestSchemeType::isValidValue($scheme)) {
			throw new Exceptions\InvalidArgumentException('Provided value is not valid');
		}

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($scheme): void {
			$qb->andWhere('n.scheme = :scheme')->setParameter('scheme', $scheme);
		};
	}

	/**
	 * @param string $host
	 *
	 * @return void
	 */
	public function byHost(string $host): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($host): void {
			$qb->andWhere('n.host = :host')->setParameter('host', $host);
		};
	}

	/**
	 * @param int $port
	 *
	 * @return void
	 */
	public function byPort(int $port): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($port): void {
			$qb->andWhere('n.port = :port')->setParameter('port', $port);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Routes\Nodes\Node> $repository
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
	 * @param ORM\EntityRepository<Entities\Routes\Nodes\Node> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(n.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Routes\Nodes\Node> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('n');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
