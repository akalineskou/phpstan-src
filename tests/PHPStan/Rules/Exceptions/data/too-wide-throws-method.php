<?php

namespace TooWideThrowsMethod;

use DomainException;

class Foo
{

	/** @throws \InvalidArgumentException */
	public function doFoo(): void // ok
	{
		throw new \InvalidArgumentException();
	}

	/** @throws \LogicException */
	public function doFoo2(): void // ok
	{
		throw new \InvalidArgumentException();
	}

	/** @throws \InvalidArgumentException */
	public function doFoo3(): void // ok
	{
		throw new \LogicException();
	}

	/** @throws \InvalidArgumentException|\DomainException */
	public function doFoo4(): void // error - DomainException unused
	{
		throw new \InvalidArgumentException();
	}

	/** @throws void */
	public function doFoo5(): void // ok - picked up by different rule
	{
		throw new \InvalidArgumentException();
	}

	/** @throws \InvalidArgumentException|\DomainException */
	public function doFoo6(): void // ok
	{
		if (rand(0, 1)) {
			throw new \InvalidArgumentException();
		}

		throw new DomainException();
	}

	/** @throws \DomainException */
	public function doFoo7(): void // error - DomainException unused
	{
		throw new \InvalidArgumentException();
	}

	/**
	 * @throws \InvalidArgumentException
	 * @throws \DomainException
	 */
	public function doFoo8(): void // error - DomainException unused
	{
		throw new \InvalidArgumentException();
	}

	/** @throws \DomainException */
	public function doFoo9(): void // error - DomainException unused
	{

	}

}

class ParentClass
{

	/** @throws \LogicException */
	public function doFoo(): void
	{

	}

}

class ChildClass extends ParentClass
{

	public function doFoo(): void
	{

	}

}