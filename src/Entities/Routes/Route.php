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

namespace FastyBird\GatewayNode\Entities\Routes;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
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
 *     name="fb_routes",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Routes mapping"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="route_name_unique", columns={"route_name"}),
 *       @ORM\UniqueConstraint(name="route_path_unique", columns={"route_path", "route_method"})
 *     },
 *     indexes={
 *       @ORM\Index(name="route_path_idx", columns={"route_path"}),
 *       @ORM\Index(name="route_method_idx", columns={"route_method"})
 *     }
 * )
 */
class Route extends Entities\Entity implements IRoute
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="route_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="route_name", length=50, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="route_path", length=200, nullable=false)
	 */
	private $path;

	/**
	 * @var Types\RequestMethodType
	 *
	 * @Enum(class=Types\RequestMethodType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="route_method", nullable=false, options={"default": "GET"})
	 */
	private $method;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="route_destination", length=200, nullable=false)
	 */
	private $destination;

	/**
	 * @var Entities\Routes\Nodes\INode
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\ManyToOne(targetEntity="FastyBird\GatewayNode\Entities\Routes\Nodes\Node", inversedBy="routes", cascade={"persist"})
	 * @ORM\JoinColumn(name="node_id", referencedColumnName="node_id", onDelete="CASCADE")
	 */
	private $node;

	/**
	 * @param string $name
	 * @param string $path
	 * @param string $destination
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $name,
		string $path,
		string $destination,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->name = $name;
		$this->path = $path;
		$this->destination = $destination;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMethod(Types\RequestMethodType $method): void
	{
		$this->method = $method;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethod(): Types\RequestMethodType
	{
		return $this->method;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDestination(string $destination): void
	{
		$this->destination = $destination;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDestination(): string
	{
		return $this->destination;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setNode(Entities\Routes\Nodes\INode $node): void
	{
		$this->node = $node;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNode(): Entities\Routes\Nodes\INode
	{
		return $this->node;
	}

}
