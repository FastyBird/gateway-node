<?php declare(strict_types = 1);

/**
 * RoutesManager.php
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
use FastyBird\GatewayNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Routes entities manager
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RoutesManager implements IRoutesManager
{

	use Nette\SmartObject;

	/** @var Crud\IEntityCrud */
	private Crud\IEntityCrud $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud
	) {
		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Routes\IRoute {
		// Get entity creator
		/** @var Entities\Routes\IRoute $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Routes\IRoute $entity,
		Utils\ArrayHash $values
	): Entities\Routes\IRoute {
		/** @var Entities\Routes\IRoute $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Routes\IRoute $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
