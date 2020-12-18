<?php declare(strict_types = 1);

/**
 * NodesManager.php
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

namespace FastyBird\GatewayNode\Models\Routes\Nodes;

use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Nodes entities manager
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class NodesManager implements INodesManager
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
	): Entities\Routes\Nodes\INode {
		// Get entity creator
		/** @var Entities\Routes\Nodes\INode $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Routes\Nodes\INode $entity,
		Utils\ArrayHash $values
	): Entities\Routes\Nodes\INode {
		/** @var Entities\Routes\Nodes\INode $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Routes\Nodes\INode $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
