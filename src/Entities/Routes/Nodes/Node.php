<?php declare(strict_types = 1);

/**
 * Route.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Entities\Routes\Nodes;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\Common;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Types;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_routes_nodes",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Routes nodes"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="node_unique", columns={"node_scheme", "node_host", "node_port"})
 *     }
 * )
 */
class Node extends Entities\Entity implements INode
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="node_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Types\RequestSchemeType
	 *
	 * @Enum(class=Types\RequestSchemeType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="node_scheme", nullable=false, options={"default": "http"})
	 */
	private $scheme;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="node_host", length=10, nullable=false)
	 */
	private $host;

	/**
	 * @var int
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="integer", name="node_port", length=15, nullable=FALSE, options={"default": 8000})
	 */
	private $port = 8000;

	/**
	 * @var Common\Collections\Collection<int, Entities\Routes\IRoute>
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToMany(targetEntity="FastyBird\GatewayNode\Entities\Routes\Route", mappedBy="node", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private $routes;

	/**
	 * @param Types\RequestSchemeType $scheme
	 * @param string $host
	 * @param int $port
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Types\RequestSchemeType $scheme,
		string $host,
		int $port,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->scheme = $scheme;
		$this->host = $host;
		$this->port = $port;

		$this->routes = new Common\Collections\ArrayCollection();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setScheme(Types\RequestSchemeType $scheme): void
	{
		$this->scheme = $scheme;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getScheme(): Types\RequestSchemeType
	{
		return $this->scheme;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setHost(string $host): void
	{
		$this->host = $host;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPort(int $port): void
	{
		$this->port = $port;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPort(): int
	{
		return $this->port;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRoutes(array $routes = []): void
	{
		$this->routes = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Routes\IRoute $entity */
		foreach ($routes as $entity) {
			if (!$this->routes->contains($entity)) {
				// ...and assign them to collection
				$this->routes->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addRoute(Entities\Routes\IRoute $route): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->routes->contains($route)) {
			// ...and assign it to collection
			$this->routes->add($route);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoutes(): array
	{
		return $this->routes->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeRoute(Entities\Routes\IRoute $route): void
	{
		// Check if collection contain removing entity...
		if ($this->routes->contains($route)) {
			// ...and remove it from collection
			$this->routes->removeElement($route);
		}
	}

}
