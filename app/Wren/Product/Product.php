<?php


namespace Wren\Product;


use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Wren\Exception\InvalidValueException;

class Product
{
	/** @var string */
	private $code;

	/** @var string */
	private $name;

	/** @var string */
	private $desc;

	/** @var int */
	private $stock;

	/** @var float */
	private $price;

	/** @var bool */
	private $discontinued;

	public function __construct($code, $name, $desc, $stock, $price, $discontinued)
	{
		$this->code = $code;
		$this->name = $name;
		$this->desc = $desc;
		$this->stock = $stock;
		$this->price = $price;
		$this->discontinued = $discontinued;
	}


	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDesc()
	{
		return $this->desc;
	}

	/**
	 * @return int
	 */
	public function getStock()
	{
		return $this->stock;
	}

	/**
	 * @return float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return boolean
	 */
	public function isDiscontinued()
	{
		return $this->discontinued;
	}

	public function insertArray()
	{
		return [
			'strProductName' => $this->name,
			'strProductDesc' => $this->desc,
			'strProductCode' => $this->code,
			'dtmAdded' => new DateTime(),
			'dtmDiscontinued' => $this->isDiscontinued() ? new DateTime() : NULL,
			'intStock' => $this->stock,
			'dcmPrice' => $this->price,
		];
	}

	public function validateLength($field, $length)
	{
		$realLength = Strings::length($this->$field);
		if ($realLength > $length) {
			throw new InvalidValueException(sprintf('Invalid %s length. Expected %d got %d', $field, $length, $realLength));
		}
	}

	public function isLower($field, $value) {
		return $this->$field < $value;
	}

	public function isGreater($field, $value) {
		return $this->$field > $value;
	}
}
