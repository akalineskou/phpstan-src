<?php declare(strict_types = 1);

namespace PHPStan\Rules\Classes;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\ClassCaseSensitivityCheck;
use PHPStan\Rules\ClassNameNodePair;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Class_>
 */
class ExistingClassInClassExtendsRule implements \PHPStan\Rules\Rule
{

	private \PHPStan\Rules\ClassCaseSensitivityCheck $classCaseSensitivityCheck;

	public function __construct(ClassCaseSensitivityCheck $classCaseSensitivityCheck)
	{
		$this->classCaseSensitivityCheck = $classCaseSensitivityCheck;
	}

	public function getNodeType(): string
	{
		return Node\Stmt\Class_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->extends === null) {
			return [];
		}
		return $this->classCaseSensitivityCheck->checkClassNames([new ClassNameNodePair((string) $node->extends, $node->extends)]);
	}

}
