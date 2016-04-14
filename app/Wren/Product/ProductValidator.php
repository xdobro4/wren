<?php

namespace Wren\Product;

use Nette\Utils\Strings;
use Wren\Exception\InvalidRuleException;
use Wren\Exception\InvalidValueException;
use Wren\Exception\OutOfRangeException;

class ProductValidator
{
	/** expected columns in CSV */
	const COLUMNS = 6;

	const LENGTHS = ['name' => 50, 'desc' => 255, 'code' => 10,];
	const RULES = [
		[
			'field' => 'price',
			'value' => 5,
			'validator' => self::MIN_RULE,
			'and' => [
				'field' => 'stock',
				'value' => 10,
				'validator' => self::MIN_RULE,
			],
		],
		[
			'field' => 'price',
			'value' => 1000,
			'validator' => self::MAX_RULE,
		],
	];

	const MIN_RULE = 'min';
	const MAX_RULE = 'max';

	/**
	 * @param array $product
	 *
	 * @return Product
	 *
	 * @throws InvalidValueException
	 */
	public function validate(array $product)
	{
		if (count($product) !== self::COLUMNS) {
			throw new InvalidValueException(sprintf('Invalid product'));
		}

		foreach ($product as $i => $value) {
			$product[$i] = trim($value);
		}
		$entity = new Product($product[0], $product[1], $product[2], $product[3], $product[4], $product[5] === 'yes');

		if (Strings::match($entity->getCode(), '~\W~')) {
			throw new InvalidValueException(sprintf('Invalid product code: %s', $entity->getCode()));
		}

		if (!Strings::match($entity->getStock(), '~^\d+$~')) {
			throw new InvalidValueException(sprintf('Invalid product stock count: %s', $entity->getStock()));
		}

		if (!Strings::match($entity->getPrice(), '~^[0-9]{1,}(\.[0-9]{1,})?$~')) {
			throw new InvalidValueException(sprintf('Invalid product price: %s', $entity->getPrice()));
		}

		foreach (self::LENGTHS as $field => $length) {
			$entity->validateLength($field, $length);
		}

		$this->validateForLimits($entity);

		return $entity;
	}

	private function validateForLimits(Product $entity)
	{
		foreach (self::RULES as $rule) {
			try {
				$this->validateRule($entity, $rule['validator'], $rule['field'], $rule['value']);
			} catch (OutOfRangeException $e) {
				// if combined condition muss be checked again example: price < 5 and stock > 10 is valid but price < 5 and stock 5 is invalid
				if (isset($rule['and'])) {
					$next = $rule['and'];
					$this->validateRule($entity, $next['validator'], $next['field'], $next['value']);
				}
				else {
					throw $e;
				}
			}
		}
	}

	/**
	 * @param Product $entity
	 * @param $validator
	 * @param $field
	 * @param $value
	 *
	 * @throws InvalidRuleException
	 * @throws OutOfRangeException
	 */
	private function validateRule(Product $entity, $validator, $field, $value)
	{
		switch ($validator) {
			case self::MIN_RULE:
				if (!$entity->isGreater($field, $value)) {
					throw new OutOfRangeException(sprintf('%s is supposted to be greater than %d', $field, $value));
				}
				break;
			case self::MAX_RULE:
				if (!$entity->isLower($field, $value)) {
					throw new OutOfRangeException(sprintf('%s is suppousted to be lower than %d', $field, $value));
				}
				break;
			default:
				throw new InvalidRuleException(sprintf('Unknown rule %s', $validator));
		}
	}

}
