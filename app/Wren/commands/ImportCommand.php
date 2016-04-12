<?php

namespace Wren\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{

	const COMMAND_NAME = 'wren:import:csv';

	protected function configure()
	{
		parent::configure();

		$this->setName(self::COMMAND_NAME)
			->setDescription('Import from csv')
			->addOption('file', NULL, InputArgument::OPTIONAL, 'Path to import file', __DIR__ . '/../../../task/stock.csv');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getOption('file');
		$output->writeln($file);

		if (($handle = @fopen($file, "r")) === FALSE) {
			$output->write('<error>File not found!</error>');
			return;
		}

		$row = 1;
		while (($data = fgetcsv($handle)) !== FALSE) {
			var_dump($data);
		}
		fclose($handle);

		$output->writeln('Thank u!');
	}

}
