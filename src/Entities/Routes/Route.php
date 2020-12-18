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
 *     name="fb_routes",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Routes mapping"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="route_name_unique", columns={"route_name"}),
 *       @ORM\UniqueConstraint(name="route_path_unique", columns={"route_method", "route_path"})
 *     },
 *     indexes={
 *       @ORM\Index(name="route_method_idx", columns={"route_method"}),
 *       @ORM\Index(name="route_path_idx", columns={"route_path"})
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
	protected Uuid\UuidInterface $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\Column(type="string", name="route_name", length=50, nullable=false)
	 */
	private string $name;

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
	 * @ORM\Column(type="string", name="route_path", length=250, nullable=false)
	 */
	private string $path;

	/**
	 * @var string[]
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="json", name="route_headers", nullable=true)
	 */
	private array $headers = [];

	/**
	 * @var Common\Collections\Collection<int, Entities\Routes\Destinations\IDestination>
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\OneToMany(targetEntity="FastyBird\GatewayNode\Entities\Routes\Destinations\Destination", mappedBy="route", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private Common\Collections\Collection $destinations;

	/**
	 * @param string $name
	 * @param Types\RequestMethodType $method
	 * @param string $path
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $name,
		Types\RequestMethodType $method,
		string $path,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->name = $name;
		$this->method = $method;
		$this->path = $path;

		$this->destinations = new Common\Collections\ArrayCollection();
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
	public function setDestinations(array $destinations = []): void
	{
		$this->destinations = new Common\Collections\ArrayCollection();

		// Process all passed entities...
		/** @var Entities\Routes\Destinations\IDestination $entity */
		foreach ($destinations as $entity) {
			if (!$this->destinations->contains($entity)) {
				// ...and assign them to collection
				$this->destinations->add($entity);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function addDestination(Entities\Routes\Destinations\IDestination $destination): void
	{
		// Check if collection does not contain inserting entity
		if (!$this->destinations->contains($destination)) {
			// ...and assign it to collection
			$this->destinations->add($destination);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDestinations(): array
	{
		return $this->destinations->toArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeDestination(Entities\Routes\Destinations\IDestination $destination): void
	{
		// Check if collection contain removing entity...
		if ($this->destinations->contains($destination)) {
			// ...and remove it from collection
			$this->destinations->removeElement($destination);
		}
	}

}
