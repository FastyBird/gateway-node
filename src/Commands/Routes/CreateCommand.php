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
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Types;
use Nette;
use Nette\Utils;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

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

	/** @var Models\Routes\IRoutesManager */
	private $routesManager;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'commands.routeCreate';

	/**
	 * @param Models\Routes\IRoutesManager $routesManager
	 * @param Translation\Translator $translator
	 * @param string|null $name
	 */
	public function __construct(
		Models\Routes\IRoutesManager $routesManager,
		Translation\Translator $translator,
		?string $name = null
	) {
		// Modules models
		$this->routesManager = $routesManager;

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
			->addArgument('name', Input\InputArgument::OPTIONAL, $this->translator->translate('name.title'))
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
			$name = $io->ask($this->translator->translate('inputs.name.title'));
		}

		if ($input->hasArgument('path') && $input->getArgument('path')) {
			$path = $input->getArgument('path');

		} else {
			$path = $io->ask($this->translator->translate('inputs.path.title'));
		}

		$method = $io->choice($this->translator->translate('inputs.method.title'), ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'], 'GET');

		if ($input->hasArgument('destination') && $input->getArgument('destination')) {
			$destination = $input->getArgument('destination');

		} else {
			$destination = $io->ask($this->translator->translate('inputs.destination.title'));
		}

		try {
			$createRoute = new Utils\ArrayHash();
			$createRoute->offsetSet('name', $name);
			$createRoute->offsetSet('path', $path);
			$createRoute->offsetSet('destination', $destination);
			$createRoute->offsetSet('method', Types\KeyStateType::get($method));

			$route = $this->routesManager->create($createRoute);

			$io->text(sprintf('<info>%s</info>', $this->translator->translate('success', ['name' => $route->getName()])));

		} catch (Throwable $ex) {
			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.route.wasNotCreated', ['error' => $ex->getMessage()])));
		}

		return 0;
	}

}
