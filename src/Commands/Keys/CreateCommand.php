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

namespace FastyBird\GatewayNode\Commands\Keys;

use Contributte\Translation;
use FastyBird\GatewayNode\Models;
use FastyBird\GatewayNode\Types;
use Nette;
use Nette\Utils;
use Ramsey\Uuid;
use Symfony\Component\Console;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;
use Symfony\Component\Console\Style;
use Throwable;

/**
 * API key creation command
 *
 * @package        FastyBird:GatewayNode!
 * @subpackage     Commands
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class CreateCommand extends Console\Command\Command
{

	use Nette\SmartObject;

	/** @var Models\Keys\IKeysManager */
	private $keysManager;

	/** @var Translation\PrefixedTranslator */
	private $translator;

	/** @var string */
	private $translationDomain = 'commands.keyCreate';

	/**
	 * @param Models\Keys\IKeysManager $keysManager
	 * @param Translation\Translator $translator
	 * @param string|null $name
	 */
	public function __construct(
		Models\Keys\IKeysManager $keysManager,
		Translation\Translator $translator,
		?string $name = null
	) {
		// Modules models
		$this->keysManager = $keysManager;

		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);

		parent::__construct($name);
	}

	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this
			->setName('fb:gateway-node:keys:create')
			->addArgument('name', Input\InputArgument::OPTIONAL, $this->translator->translate('name.title'))
			->addOption('noconfirm', null, Input\InputOption::VALUE_NONE, 'do not ask for any confirmation')
			->setDescription('Create API access key.');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
	{
		$io = new Style\SymfonyStyle($input, $output);

		$io->title('FB gateway node - create key');

		if ($input->hasArgument('name') && $input->getArgument('name')) {
			$name = $input->getArgument('name');

		} else {
			$name = $io->ask($this->translator->translate('inputs.name.title'));
		}

		try {
			$createKey = new Utils\ArrayHash();
			$createKey->offsetSet('name', $name);
			$createKey->offsetSet('key', Uuid\Uuid::uuid4());
			$createKey->offsetSet('state', Types\KeyStateType::get(Types\KeyStateType::STATE_ACTIVE));

			$key = $this->keysManager->create($createKey);

			$io->text(sprintf('<info>%s</info>', $this->translator->translate('success', ['name' => $key->getName(), 'key' => $key->getKey()])));

		} catch (Throwable $ex) {
			$io->text(sprintf('<error>%s</error>', $this->translator->translate('validation.key.wasNotCreated', ['error' => $ex->getMessage()])));
		}

		return 0;
	}

}
