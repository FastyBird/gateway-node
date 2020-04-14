<?php declare(strict_types = 1);

/**
 * KeyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 * @since          0.1.3
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Models\Keys;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\GatewayNode\Entities;
use Nette;

/**
 * Key repository
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class KeyRepository implements IKeyRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Keys\Key>|null */
	private $repository = null;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByIdentifier(string $identifier): ?Entities\Keys\IKey
	{
		/** @var Entities\Keys\IKey|null $key */
		$key = $this->getRepository()->findOneBy(['id' => $identifier]);

		return $key;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneByKey(string $key): ?Entities\Keys\IKey
	{
		/** @var Entities\Keys\IKey|null $key */
		$key = $this->getRepository()->findOneBy(['key' => $key]);

		return $key;
	}

	/**
	 * @return ORM\EntityRepository<Entities\Keys\Key>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Keys\Key::class);
		}

		return $this->repository;
	}

}
