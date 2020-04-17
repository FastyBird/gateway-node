<?php declare(strict_types = 1);

/**
 * CreateCommand.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:GatewayNode!
 * @subpackage     Commands
 * @since          0.1.0
 *
 * @date           14.04.20
 */

namespace FastyBird\GatewayNode\Commands\Routes;

use Contributte\Translation;
use FastyBird\GatewayNode\Entities;
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Queries;
use FastyBird\GatewayNode\Types;
use Nette;
use Nette\Utils;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;
use Tracy\Debugger;

/**
 * Routes creation command
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class CreateCommand extends Console\Command\Command
{

	use Nette\SmartObject;

	/** @var Models\Routes\IRouteRepository */
	private $routeRepository;

	/** @var Models\Routes\IRoutesManager */
	private $routesManager;

	/** @var Models\Routes\Nodes\INodeRepository */
	private $nodeRepository;

	/** @var Models\Routes\Destinations\IDestinationsManager */
	private $destinationsManager;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'commands.routeCreate';

	/**
	 * @param Models\Routes\IRouteRepository $routeRepository
	 * @param Models\Routes\IRoutesManager $routesManager
	 * @param Models\Routes\Nodes\INodeRepository $nodeRepository
	 * @param Models\Routes\Destinations\IDestinationsManager $destinationsManager
	 * @param Translation\Translator $translator
	 * @param string|null $name
	 */
	public function __construct(
		Models\Routes\IRouteRepository $routeRepository,
		Models\Routes\IRoutesManager $routesManager,
		Models\Routes\Nodes\INodeRepository $nodeRepository,
		Models\Routes\Destinations\IDestinationsManager $destinationsManager,
		Translation\Translator $translator,
		?string $name = null
	) {
		// Modules models
		$this->routeRepository = $routeRepository;
		$this->routesManager = $routesManager;
		$this->nodeRepository = $nodeRepository;
		$this->destinationsManager = $destinationsManager;

		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);

		parent::__construct($name);
	}

	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:gateway-node:routes:create')
			->addArgument('name', Input\InputArgument::OPTIONAL, $this->translator->translate('inputs.routeName.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Create route mapping.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB gateway node - create route');

		if ($input->hasArgument('name') && $input->getArgument('name')) {
			$name = $input->getArgument('name');

		} else {
			$name = $io->ask($this->translator->translate('inputs.routeName.title'));
		}

		if ($input->hasArgument('path') && $input->getArgument('path')) {
			$path = $input->getArgument('path');

		} else {
			$path = $io->ask($this->translator->translate('inputs.routePath.title'));
		}

		$routeMethod = $io->choice(
			$this->translator->translate('inputs.routeMethod.title'),
			[
				Types\RequestMethodType::METHOD_GET,
				Types\RequestMethodType::METHOD_POST,
				Types\RequestMethodType::METHOD_PATCH,
				Types\RequestMethodType::METHOD_PUT,
				Types\RequestMethodType::METHOD_DELETE,
			],
			Types\RequestMethodType::METHOD_GET
		);

		if ($input->hasArgument('destination') && $input->getArgument('destination')) {
			$destinationPath = $input->getArgument('destination');

		} else {
			$destinationPath = $io->ask($this->translator->translate('inputs.destinationPath.title'));
		}

		$destinationMethod = $io->choice(
			$this->translator->translate('inputs.destinationMethod.title'),
			[
				Types\RequestMethodType::METHOD_GET,
				Types\RequestMethodType::METHOD_POST,
				Types\RequestMethodType::METHOD_PATCH,
				Types\RequestMethodType::METHOD_PUT,
				Types\RequestMethodType::METHOD_DELETE,
			],
			$routeMethod
		);

		$destinationScheme = $io->choice(
			$this->translator->translate('inputs.destinationScheme.title'),
			[
				Types\RequestSchemeType::METHOD_HTTP,
				Types\RequestSchemeType::METHOD_HTTPS,
			],
			Types\RequestSchemeType::METHOD_HTTP
		);

		if ($input->hasArgument('host') && $input->getArgument('host')) {
			$destinationHost = $input->getArgument('host');

		} else {
			$destinationHost = $io->ask($this->translator->translate('inputs.destinationHost.title'));
		}

		if ($input->hasArgument('port') && $input->getArgument('port')) {
			$destinationPort = $input->getArgument('port');

		} else {
			$destinationPort = $io->ask($this->translator->translate('inputs.destinationPort.title'));
		}

		try {
			// Try to find node by given details
			$findQuery = new Queries\FindRouteNodeQuery();
			$findQuery->byScheme($destinationScheme);
			$findQuery->byHost($destinationHost);
			$findQuery->byPort((int) $destinationPort);

			$node = $this->nodeRepository->findOneBy($findQuery);

			if ($node === null) {
				// Node is not created, create new one
				$node = new Utils\ArrayHash();
				$node->offsetSet('entity', Entities\Routes\Nodes\Node::class);
				$node->offsetSet('name', $destinationHost . ':' . $destinationPort);
				$node->offsetSet('scheme', Types\RequestSchemeType::get($destinationScheme));
				$node->offsetSet('host', $destinationHost);
				$node->offsetSet('port', (int) $destinationPort);
			}

			$destination = new Utils\ArrayHash();
			$destination->offsetSet('path', $destinationPath);
			$destination->offsetSet('method', Types\RequestMethodType::get($destinationMethod));
			$destination->offsetSet('node', $node);

			$findQuery = new Queries\FindRouteQuery();
			$findQuery->byPath($path);
			$findQuery->byMethod($routeMethod);

			$route = $this->routeRepository->findOneBy($findQuery);

			if ($route !== null) {
				$destination->offsetSet('route', $route);

				$this->destinationsManager->create($destination);

				$io->text(sprintf('<info>%s</info>', $this->translator->translate('success.updated', ['name' => $route->getName()])));

			} else {
				$createRoute = new Utils\ArrayHash();
				$createRoute->offsetSet('name', $name);
				$createRoute->offsetSet('path', $path);
				$createRoute->offsetSet('method', Types\RequestMethodType::get($routeMethod));
				$createRoute->offsetSet('destinations', [$destination]);

				$route = $this->routesManager->create($createRoute);

				$io->text(sprintf('<info>%s</info>', $this->translator->translate('success.created', ['name' => $route->getName()])));
			}

		} catch (Throwable $ex) {
			Debugger::log($ex);
			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.route.wasNotCreated', ['error' => $ex->getMessage()])));
		}

		return 0;
	}

}
