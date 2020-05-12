<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Php;

use PHPStan\Reflection\ClassReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

class UniversalObjectCrateProperty implements \PHPStan\Reflection\PropertyReflection
{

	private \PHPStan\Reflection\ClassReflection $declaringClass;

	private \PHPStan\Type\Type $readableType;

	private \PHPStan\Type\Type $writableType;

	public function __construct(
		ClassReflection $declaringClass,
		Type $readableType,
		Type $writableType
	)
	{
		$this->declaringClass = $declaringClass;
		$this->readableType = $readableType;
		$this->writableType = $writableType;
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->declaringClass;
	}

	public function isStatic(): bool
	{
		return false;
	}

	public function isPrivate(): bool
	{
		return false;
	}

	public function isPublic(): bool
	{
		return true;
	}

	public function getReadableType(): Type
	{
		return $this->readableType;
	}

	public function getWritableType(): Type
	{
		return $this->writableType;
	}

	public function canChangeTypeAfterAssignment(): bool
	{
		return true;
	}

	public function isReadable(): bool
	{
		return true;
	}

	public function isWritable(): bool
	{
		return true;
	}

	public function isDeprecated(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getDeprecatedDescription(): ?string
	{
		return null;
	}

	public function isInternal(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getDocComment(): ?string
	{
		return null;
	}

}
