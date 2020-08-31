<?php declare(strict_types = 1);

/**
 * Node.php
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

use Nette\SmartObject;

/**
 * Node info
 *
 * @package          FastyBird:GatewayNode!
 * @subpackage       NodesCollection
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Node
{

	use SmartObject;

	/** @var string */
	private $name;

	/** @var string */
	private $host;

	/** @var int */
	private $port;

	/** @var bool */
	private $secured;

	/** @var string */
	private $prefix;

	public function __construct(
		string $name,
		string $host,
		int $port,
		bool $secured,
		string $prefix
	) {
		$this->name = $name;
		$this->host = $host;
		$this->port = $port;
		$this->secured = $secured;
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * @return int
	 */
	public function getPort(): int
	{
		return $this->port;
	}

	/**
	 * @return bool
	 */
	public function isSecured(): bool
	{
		return $this->secured;
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string
	{
		return $this->prefix;
	}

}
