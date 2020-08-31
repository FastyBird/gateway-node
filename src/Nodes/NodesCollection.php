<?php declare(strict_types = 1);

/**
 * NodesCollection.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     NodesCollection
 * @since          0.1.3
 *
 * @date           31.08.20
 */

namespace FastyBird\GatewayNode\Nodes;

use FastyBird\GatewayNode\Exceptions;
use Nette\SmartObject;
use SplObjectStorage;

/**
 * NodesCollection collection
 *
 * @package          FastyBird:GatewayNode!
 * @subpackage       NodesCollection
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 */
class NodesCollection
{

	use SmartObject;

	/** @var SplObjectStorage */
	private $nodes;

	public function __construct()
	{
		$this->nodes = new SplObjectStorage();
	}

	/**
	 * @param Node $node
	 *
	 * @return void
	 */
	public function addNode(Node $node): void
	{
		if (!$this->nodes->contains($node)) {
			/** @var Node $existingNode */
			foreach ($this->nodes as $existingNode) {
				if (
					$existingNode->getHost() === $node->getHost()
					&& $existingNode->getPort() === $node->getPort()
				) {
					throw new Exceptions\InvalidArgumentException('Node with same host & port is already registered.');
				}
			}

			$this->nodes->attach($node);
		}
	}

	/**
	 * @return SplObjectStorage
	 */
	public function getNodes(): SplObjectStorage
	{
		return $this->nodes;
	}

	/**
	 * @param string $name
	 *
	 * @return Node|null
	 */
	public function getByName(string $name): ?Node
	{
		/** @var Node $node */
		foreach ($this->nodes as $node) {
			if ($node->getName() === $name) {
				return $node;
			}
		}

		return null;
	}

}
