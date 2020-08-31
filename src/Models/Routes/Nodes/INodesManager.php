<?php declare(strict_types = 1);

/**
 * INodesManager.php
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
use Nette\Utils;

/**
 * Nodes entities manager interface
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface INodesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\Nodes\INode
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Routes\Nodes\INode;

	/**
	 * @param Entities\Routes\Nodes\INode $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Routes\Nodes\INode
	 */
	public function update(
		Entities\Routes\Nodes\INode $entity,
		Utils\ArrayHash $values
	): Entities\Routes\Nodes\INode;

	/**
	 * @param Entities\Routes\Nodes\INode $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Routes\Nodes\INode $entity
	): bool;

}
