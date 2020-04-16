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

namespace FastyBird\GatewayNode\Entities\Routes\Destinations;

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
 *     name="fb_routes_destinations",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Routes destinations"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="destination_path_unique", columns={"destination_method", "destination_path"})
 *     },
 *     indexes={
 *       @ORM\Index(name="destination_method_idx", columns={"destination_method"}),
 *       @ORM\Index(name="destination_path_idx", columns={"destination_path"})
 *     }
 * )
 */
class Destination extends Entities\Entity implements IDestination
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="destination_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var Types\RequestMethodType
	 *
	 * @Enum(class=Types\RequestMethodType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="destination_method", nullable=false, options={"default": "GET"})
	 */
	private $method;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="destination_path", length=200, nullable=false)
	 */
	private $path;

	/**
	 * @var string[]
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="json", name="destination_headers", nullable=true)
	 */
	private $headers = [];

	/**
	 * @var Entities\Routes\Nodes\INode
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\ManyToOne(targetEntity="FastyBird\GatewayNode\Entities\Routes\Nodes\Node", inversedBy="destinations", cascade={"persist"})
	 * @ORM\JoinColumn(name="node_id", referencedColumnName="node_id", onDelete="CASCADE")
	 */
	private $node;

	/**
	 * @var Entities\Routes\IRoute
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\ManyToOne(targetEntity="FastyBird\GatewayNode\Entities\Routes\Route", inversedBy="destinations", cascade={"persist"})
	 * @ORM\JoinColumn(name="route_id", referencedColumnName="route_id", onDelete="CASCADE")
	 */
	private $route;

	/**
	 * @param Types\RequestMethodType $method
	 * @param string $path
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Types\RequestMethodType $method,
		string $path,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->method = $method;
		$this->path = $path;
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
	public function setHeaders(array $headers): void
	{
		$this->headers = $headers;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHeader(string $header): void
	{
		$headers = $this->headers;
		$headers[] = $header;

		$this->headers = array_unique($headers);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHeaders(): array
	{
		return $this->headers;
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

	/**
	 * {@inheritDoc}
	 */
	public function setRoute(Entities\Routes\IRoute $route): void
	{
		$this->route = $route;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoute(): Entities\Routes\IRoute
	{
		return $this->route;
	}

}
