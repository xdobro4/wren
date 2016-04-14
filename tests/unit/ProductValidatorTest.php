<?php

use Wren\Exception\InvalidValueException;
use Wren\Exception\OutOfRangeException;

class ProductValidatorTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/** @var \Wren\Product\ProductValidator */
	protected $validator;

	protected function _before()
	{
		$this->validator = new \Wren\Product\ProductValidator();
	}

	protected function _after()
	{
	}

	public function outOfRange()
	{
		return [[
			['P1', 'P1name', 'P1desc', '9', '4', '',],
			['P2', 'P2name', 'P2desc', '0', '1000', '',],
		]];
	}

	/**
	 * @dataProvider outOfRange
	 *
	 * @param $data
	 *
	 * @throws InvalidValueException
	 */
	public function testValidationOutOfRange($data)
	{
		$this->expectException(OutOfRangeException::class);
		$this->validator->validate($data);
	}


	public function fields()
	{
		return [[
			['P 1', 'P1name', 'P1desc', '9', '4', '',], // code
			['P2', 'P2name', 'P2desc', 'zero', '4', '',], // stock
			['P3', 'P3name', 'P3desc', 'zero', '$1000', '',], // price
			['P4', 'P4name', 'P4desc', '0', '10 gbp', '',], // price
		]];
	}

	/**
	 * @dataProvider fields
	 *
	 * @param $data
	 *
	 * @throws InvalidValueException
	 */
	public function testValidationFields($data)
	{
		$this->expectException(InvalidValueException::class);
		$this->validator->validate($data);
	}
}
