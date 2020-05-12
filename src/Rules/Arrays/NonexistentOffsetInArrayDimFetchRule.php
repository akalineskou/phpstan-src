<?php declare(strict_types = 1);

namespace PHPStan\Rules\Arrays;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\ArrayDimFetch>
 */
class NonexistentOffsetInArrayDimFetchRule implements \PHPStan\Rules\Rule
{

	private RuleLevelHelper $ruleLevelHelper;

	private bool $reportMaybes;

	public function __construct(
		RuleLevelHelper $ruleLevelHelper,
		bool $reportMaybes
	)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
		$this->reportMaybes = $reportMaybes;
	}

	public function getNodeType(): string
	{
		return \PhpParser\Node\Expr\ArrayDimFetch::class;
	}

	public function processNode(\PhpParser\Node $node, Scope $scope): array
	{
		if ($node->dim !== null) {
			$dimType = $scope->getType($node->dim);
			$unknownClassPattern = sprintf('Access to offset %s on an unknown class %%s.', $dimType->describe(VerbosityLevel::value()));
		} else {
			$dimType = null;
			$unknownClassPattern = 'Access to an offset on an unknown class %s.';
		}

		$isOffsetAccessibleTypeResult = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			$unknownClassPattern,
			static function (Type $type): bool {
				return $type->isOffsetAccessible()->yes();
			}
		);
		$isOffsetAccessibleType = $isOffsetAccessibleTypeResult->getType();
		if ($isOffsetAccessibleType instanceof ErrorType) {
			return $isOffsetAccessibleTypeResult->getUnknownClassErrors();
		}

		$isOffsetAccessible = $isOffsetAccessibleType->isOffsetAccessible();

		if ($scope->isInExpressionAssign($node) && $isOffsetAccessible->yes()) {
			return [];
		}

		if (!$isOffsetAccessible->yes()) {
			if ($isOffsetAccessible->no() || $this->reportMaybes) {
				if ($dimType !== null) {
					return [
						RuleErrorBuilder::message(sprintf(
							'Cannot access offset %s on %s.',
							$dimType->describe(VerbosityLevel::value()),
							$isOffsetAccessibleType->describe(VerbosityLevel::value())
						))->build(),
					];
				}

				return [
					RuleErrorBuilder::message(sprintf(
						'Cannot access an offset on %s.',
						$isOffsetAccessibleType->describe(VerbosityLevel::typeOnly())
					))->build(),
				];
			}

			return [];
		}

		if ($dimType === null) {
			return [];
		}

		$typeResult = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			$unknownClassPattern,
			static function (Type $type) use ($dimType): bool {
				return $type->hasOffsetValueType($dimType)->yes();
			}
		);
		$type = $typeResult->getType();
		if ($type instanceof ErrorType) {
			return $typeResult->getUnknownClassErrors();
		}

		$hasOffsetValueType = $type->hasOffsetValueType($dimType);
		$report = $hasOffsetValueType->no();
		if ($hasOffsetValueType->maybe()) {
			$constantArrays = TypeUtils::getOldConstantArrays($type);
			if (count($constantArrays) > 0) {
				foreach ($constantArrays as $constantArray) {
					if ($constantArray->hasOffsetValueType($dimType)->no()) {
						$report = true;
						break;
					}
				}
			}
		}

		if (!$report && $this->reportMaybes) {
			foreach (TypeUtils::flattenTypes($type) as $innerType) {
				if ($dimType instanceof UnionType) {
					if ($innerType->hasOffsetValueType($dimType)->no()) {
						$report = true;
						break;
					}
					continue;
				}
				foreach (TypeUtils::flattenTypes($dimType) as $innerDimType) {
					if ($innerType->hasOffsetValueType($innerDimType)->no()) {
						$report = true;
						break;
					}
				}
			}
		}

		if ($report) {
			return [
				RuleErrorBuilder::message(sprintf('Offset %s does not exist on %s.', $dimType->describe(VerbosityLevel::value()), $type->describe(VerbosityLevel::value())))->build(),
			];
		}

		return [];
	}

}
