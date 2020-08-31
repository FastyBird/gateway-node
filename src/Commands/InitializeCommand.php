<?php declare(strict_types = 1);

/**
 * InitializeCommand.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           31.08.20
 */

namespace FastyBird\GatewayNode\Commands;

use Doctrine\Common;
use Doctrine\DBAL\Connection;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Exceptions;
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Nodes;
use FastyBird\GatewayNode\Queries;
use FastyBird\GatewayNode\Types;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
use Monolog;
use Nette\Utils;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * Node initialize command
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class InitializeCommand extends Console\Command\Command
{

	/** @var Models\Routes\IRoutesManager */
	private $routesManager;

	/** @var Models\Routes\IRouteRepository */
	private $routeRepository;

	/** @var Models\Routes\Nodes\INodesManager */
	private $nodesManager;

	/** @var Models\Routes\Nodes\INodeRepository */
	private $nodeRepository;

	/** @var Nodes\NodesCollection */
	private $nodesCollection;

	/** @var NodeMetadataLoaders\IMetadataLoader */
	private $metadataLoader;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		Models\Routes\IRoutesManager $routesManager,
		Models\Routes\IRouteRepository $routeRepository,
		Models\Routes\Nodes\INodesManager $nodesManager,
		Models\Routes\Nodes\INodeRepository $nodeRepository,
		Nodes\NodesCollection $nodesCollection,
		NodeMetadataLoaders\IMetadataLoader $metadataLoader,
		Common\Persistence\ManagerRegistry $managerRegistry,
		LoggerInterface $logger,
		?string $name = null
	) {
		$this->routesManager = $routesManager;
		$this->routeRepository = $routeRepository;
		$this->nodesManager = $nodesManager;
		$this->nodeRepository = $nodeRepository;

		$this->nodesCollection = $nodesCollection;
		$this->metadataLoader = $metadataLoader;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger;

		parent::__construct($name);

		// Override loggers to not log debug events into console
		if ($logger instanceof Monolog\Logger) {
			foreach ($logger->getHandlers() as $handler) {
				if ($handler instanceof Monolog\Handler\StreamHandler) {
					$handler->setLevel(Monolog\Logger::WARNING);
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:initialize')
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Initialize node.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$symfonyApp = $this->getApplication();

		if ($symfonyApp === null) {
			return 1;
		}

		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB gateway node - initialization');

		$io->note('This action will create or update node database structure, create initial data and initialize routes.');

		/** @var bool $continue */
		$continue = $io->ask('Would you like to continue?', 'n', function ($answer): bool {
			if (!in_array($answer, ['y', 'Y', 'n', 'N'], true)) {
				throw new RuntimeException('You must type Y or N');
			}

			return in_array($answer, ['y', 'Y'], true);
		});

		if (!$continue) {
			return 0;
		}

		$io->section('Preparing node database');

		$databaseCmd = $symfonyApp->find('orm:schema-tool:update');

		$result = $databaseCmd->run(new Input\ArrayInput([
			'--force' => true,
		]), $output);

		if ($result !== 0) {
			$io->error('Something went wrong, initialization could not be finished.');

			return 1;
		}

		$databaseProxiesCmd = $symfonyApp->find('orm:generate-proxies');

		$result = $databaseProxiesCmd->run(new Input\ArrayInput([
			'--quiet' => true,
		]), $output);

		if ($result !== 0) {
			$io->error('Something went wrong, initialization could not be finished.');

			return 1;
		}

		$io->newLine();

		$io->section('Preparing initial data');

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$findRoutes = new Queries\FindRouteQuery();

			$routes = $this->routeRepository->findAllBy($findRoutes);

			foreach ($routes as $route) {
				$this->routesManager->delete($route);
			}

			$findNodes = new Queries\FindRouteNodeQuery();

			$nodes = $this->nodeRepository->findAllBy($findNodes);

			foreach ($nodes as $node) {
				$this->nodesManager->delete($node);
			}

			$metadata = $this->metadataLoader->load();

			/**
			 * @var string $nodeName
			 * @var Utils\ArrayHash $nodeMetadatas
			 */
			foreach ($metadata as $nodeName => $nodeMetadatas) {
				$node = $this->nodesCollection->getByName($nodeName);

				if ($node !== null) {
					// Try to find node by given details
					$findRouteNodeQuery = new Queries\FindRouteNodeQuery();
					$findRouteNodeQuery->byScheme(Types\RequestSchemeType::get($node->isSecured() ? Types\RequestSchemeType::METHOD_HTTPS : Types\RequestSchemeType::METHOD_HTTP)->getValue());
					$findRouteNodeQuery->byHost($node->getHost());
					$findRouteNodeQuery->byPort($node->getPort());

					$routeNode = $this->nodeRepository->findOneBy($findRouteNodeQuery);

					if ($routeNode === null) {
						// Route node is not created, create new one
						$createRouteNode = Utils\ArrayHash::from([
							'entity' => Entities\Routes\Nodes\Node::class,
							'name'   => $node->getName(),
							'scheme' => Types\RequestSchemeType::get($node->isSecured() ? Types\RequestSchemeType::METHOD_HTTPS : Types\RequestSchemeType::METHOD_HTTP),
							'host'   => $node->getHost(),
							'port'   => $node->getPort(),
						]);

						$routeNode = $this->nodesManager->create($createRouteNode);
					}

					/** @var Utils\ArrayHash $nodeVersionMetadata */
					foreach ($nodeMetadatas as $nodeVersionMetadata) {
						/** @var Utils\ArrayHash $nodeMetadata */
						$nodeMetadata = $nodeVersionMetadata->offsetGet('metadata');

						/** @var Utils\ArrayHash $route */
						foreach ($nodeMetadata->offsetGet('routes') as $route) {
							$createDestination = Utils\ArrayHash::from([
								'path'    => $route->offsetGet('path'),
								'method'  => Types\RequestMethodType::get(Utils\Strings::upper($route->offsetGet('method'))),
								'headers' => $route->offsetGet('headers'),
								'node'    => $routeNode,
							]);

							$createRoute = Utils\ArrayHash::from([
								'name'         => $route->offsetGet('name'),
								'path'         => '/' . $node->getPrefix() . $route->offsetGet('path'),
								'method'       => Types\RequestMethodType::get(Utils\Strings::upper($route->offsetGet('method'))),
								'headers'      => $route->offsetGet('headers'),
								'destinations' => [$createDestination],
							]);

							$this->routesManager->create($createRoute);
						}
					}
				}
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			$this->logger->error($ex->getMessage());

			$io->error('Initial data could not be created.');

			return $ex->getCode();
		}

		$io->success('All initial data has been successfully created.');

		$io->newLine(3);

		$io->success('This node has been successfully initialized and can be now started.');

		return 0;
	}

	/**
	 * @return Connection
	 */
	protected function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
