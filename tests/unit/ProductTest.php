<?php


use Wren\Exception\InvalidValueException;

class ProductTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before()
	{
	}

	protected function _after()
	{
	}

	public function testLength()
	{
		$this->expectException(InvalidValueException::class);

		$product = new \Wren\Product\Product('P1', 'name', '', '', '', FALSE);
		$product->validateLength('name', 2);
		$product->validateLength('code', 1);
	}
}
