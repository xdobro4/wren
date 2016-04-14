<?php

namespace Wren\Commands;

use Dibi\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Wren\ImportService;

class ImportCommand extends Command
{
	const COMMAND_NAME = 'wren:import:csv';

	const CONFIG_FILE = __DIR__ . '/../config.local.yml';

	protected function configure()
	{
		parent::configure();

		$this->setName(self::COMMAND_NAME)
			->setDescription('Import from csv')
			->addOption('file', NULL, InputArgument::OPTIONAL, 'Path to import file', __DIR__ . '/../../../task/stock.csv')
			->addOption('test', 't', InputOption::VALUE_NONE, 'Test mode does not inserting anything into DB');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$filename = $input->getOption('file');
		$output->writeln(sprintf('Importing: %s', $filename));

		// initialize db connection
		$parse = Yaml::parse(file_get_contents(self::CONFIG_FILE));
		$connection = new Connection($parse['database']);

		$importService = new ImportService($output, $connection);
		$importService->setIsTestMode($input->getOption('test'));
		$importService->readFile($filename);

		$output->writeln('Done!');
	}

}
