<?php

/**
 * This file is part of the Nextras\Dbal library.
 * @license    MIT
 * @link       https://github.com/nextras/dbal
 */

namespace Nextras\Dbal\Result;

use ArrayIterator;
use IteratorAggregate;
use Nextras\Dbal\Exceptions\InvalidArgumentException;
use Nextras\Dbal\Exceptions\NotSupportedException;


final class Row implements IteratorAggregate
{
	/** @var array */
	private $data;


	public function __construct(array $data)
	{
		$this->data = $data;
	}


	public function __get($name)
	{
		if (!(isset($this->data[$name]) || array_key_exists($name, $this->data))) {
			throw new InvalidArgumentException();
		}

		return $this->data[$name];
	}


	public function __isset($name)
	{
		return isset($this->data[$name]) || array_key_exists($name, $this->data);
	}


	public function __set($name, $value)
	{
		throw new NotSupportedException('Row is read-only.');
	}


	public function __unset($name)
	{
		throw new NotSupportedException('Row is read-only.');
	}


	public function getIterator()
	{
		return new ArrayIterator($this->data);
	}


	public function __debugInfo()
	{
		return $this->data;
	}

}
