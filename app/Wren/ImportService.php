<?php

namespace Wren;

use Dibi\Connection;
use Dibi\Exception;
use Nette\Utils\Strings;
use Symfony\Component\Console\Output\OutputInterface;
use Wren\Exception\InvalidRuleException;
use Wren\Exception\InvalidValueException;
use Wren\Exception\OutOfRangeException;
use Wren\Product\Product;
use Wren\Product\ProductValidator;

class ImportService
{
	const MAX_INSERT_NUM = 100;
	const STORE_LIMIT = 1000;

	const TABLE = 'tblProductData';
	const SKIP_ON_UPDATE = ['dtmAdded'];

	/** @var Connection */
	protected $connection;

	/** @var OutputInterface */
	private $output;

	/** @var array|Product[] */
	private $products = [];

	/** @var boolean */
	private $isTestMode;

	public function __construct(OutputInterface $output, Connection $connection)
	{
		$this->output = $output;
		$this->connection = $connection;

		$this->validator = new ProductValidator();
	}

	/**
	 * read csv line by line
	 *
	 * @param $filename
	 */
	public function readFile($filename)
	{
		if (!file_exists($filename)) {
			$this->writeErrorLine('File not found!');

			return;
		}

		if (($handle = fopen($filename, "r")) === FALSE) {
			$this->writeErrorLine('Cannot open file!');

			return;
		}

		$row = $products = $valid = 0;
		while (($data = fgetcsv($handle)) !== FALSE) {
			++$row;
			if ($row == 1 && Strings::match($data[0], '~\W~')) {
				continue;
			}
			++$products;

			try {
				$item = $this->validator->validate($data);
				$this->storeProduct($item);
				++$valid;
			} catch (InvalidValueException $e) {
				$this->writeErrorLine(sprintf('%s at line: %d', $e->getMessage(), $row));
			} catch (OutOfRangeException $e) {
				$this->writeInfoLine(sprintf('%s at line: %d', $e->getMessage(), $row));
			} catch (InvalidRuleException $e) {
				$this->writeErrorLine($e->getMessage());
				fclose($handle);

				return; // in this case will be never valid
			}

			if (count($this->products) >= self::STORE_LIMIT) {
				$this->flush();
			}
		}
		fclose($handle);

		try {
			$this->flush();
		} catch (Exception $e) { // catching mysql exceptions
			$this->writeErrorLine($e->getMessage());

			return;
		}

		$this->writeInfoLine(sprintf('Imported %d from %d', $valid, $products));
	}

	private function storeProduct(Product $item)
	{
		$this->products[$item->getCode()] = $item;
	}

	/**
	 * insert into db
	 */
	private function flush()
	{
		if (empty($this->products) || $this->isTestMode) return;

		$insert = [];
		foreach ($this->products as $product) {
			$insert[] = $product->insertArray();
		}

		$onDuplicate = [];
		foreach (array_keys(end($insert)) as $key) {
			if (in_array($key, self::SKIP_ON_UPDATE)) continue;
			$onDuplicate[] = sprintf('[%s] = VALUES([%s])', $key, $key);
		}

		$i = 0;
		while ($slice = array_slice($insert, $i, self::MAX_INSERT_NUM, TRUE)) {
			$i += self::MAX_INSERT_NUM;
			$this->connection->query('INSERT INTO %n %ex', self::TABLE, $slice, 'ON DUPLICATE KEY UPDATE ' . implode(', ', $onDuplicate));
		}

		$this->products = [];
	}

	/**
	 * @param $message
	 */
	private function writeErrorLine($message)
	{
		$this->writeInfoLine(sprintf('<error>%s</error>', $message));
	}

	/**
	 * @param $message
	 */
	private function writeInfoLine($message)
	{
		$this->output->writeln(sprintf('%s', $message));
	}

	/**
	 * @param boolean $isTestMode
	 */
	public function setIsTestMode($isTestMode)
	{
		$this->isTestMode = $isTestMode;
	}
}
