<?php


use Symfony\Component\Console\Output\Output;

class ImportServiceTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/** @var \Wren\ImportService */
	protected $importService;

	protected function _before()
	{
		/** @var Output|\Mockery\Mock $output */
		$output = Mockery::mock(Output::class);
		$output->shouldReceive('writeln')->with('<error>Invalid product stock count:  at line: 8</error>')->once();
		$output->shouldReceive('writeln')->with('<error>Invalid product at line: 12</error>')->once();
		$output->shouldReceive('writeln')->with('<error>Invalid product price: $4.33 at line: 16</error>')->once();
		$output->shouldReceive('writeln')->with('<error>Invalid product at line: 18</error>')->once();
		$output->shouldReceive('writeln')->with('price is suppousted to be lower than 1000 at line: 28')->once();
		$output->shouldReceive('writeln')->with('price is suppousted to be lower than 1000 at line: 29')->once();
		$output->shouldReceive('writeln')->with('stock is supposted to be greater than 10 at line: 30')->once();
		$output->shouldReceive('writeln')->with('Imported 22 from 29')->once();

		$connection = new \Dibi\Connection([
			'driver' => 'pdo',
			'dsn' => $this->getModule('Db')->_getConfig('dsn'),
			'user' => $this->getModule('Db')->_getConfig('user'),
			'password' => $this->getModule('Db')->_getConfig('password'),
		]);
		$this->importService = new \Wren\ImportService($output, $connection);
	}

	// tests
	public function testImport()
	{
		$this->importService->readFile(__DIR__ . '/../_data/stock.csv');
		$this->tester->seeInDatabase('tblproductdata', [
			'strProductCode' => 'P0001',
			'strProductName' => 'TV',
			'strProductDesc' => '32” Tv',
			'dcmPrice' => '399.9900',
		]);
		$this->tester->dontSeeInDatabase('tblproductdata', [
			'strProductCode' => 'P0015',
		]);
	}
}
