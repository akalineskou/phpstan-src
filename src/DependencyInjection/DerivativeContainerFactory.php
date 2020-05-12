<?php declare(strict_types = 1);

namespace PHPStan\DependencyInjection;

class DerivativeContainerFactory
{

	private string $currentWorkingDirectory;

	private string $tempDirectory;

	/** @var string[] */
	private array $additionalConfigFiles;

	/** @var string[] */
	private array $analysedPaths;

	/** @var string[] */
	private array $composerAutoloaderProjectPaths;

	/** @var string[] */
	private array $analysedPathsFromConfig;

	/** @var string[] */
	private array $allCustomConfigFiles;

	private string $usedLevel;

	/**
	 * @param string $currentWorkingDirectory
	 * @param string $tempDirectory
	 * @param string[] $additionalConfigFiles
	 * @param string[] $analysedPaths
	 * @param string[] $composerAutoloaderProjectPaths
	 * @param string[] $analysedPathsFromConfig
	 * @param string[] $allCustomConfigFiles
	 * @param string $usedLevel
	 */
	public function __construct(
		string $currentWorkingDirectory,
		string $tempDirectory,
		array $additionalConfigFiles,
		array $analysedPaths,
		array $composerAutoloaderProjectPaths,
		array $analysedPathsFromConfig,
		array $allCustomConfigFiles,
		string $usedLevel
	)
	{
		$this->currentWorkingDirectory = $currentWorkingDirectory;
		$this->tempDirectory = $tempDirectory;
		$this->additionalConfigFiles = $additionalConfigFiles;
		$this->analysedPaths = $analysedPaths;
		$this->composerAutoloaderProjectPaths = $composerAutoloaderProjectPaths;
		$this->analysedPathsFromConfig = $analysedPathsFromConfig;
		$this->allCustomConfigFiles = $allCustomConfigFiles;
		$this->usedLevel = $usedLevel;
	}

	/**
	 * @param string[] $additionalConfigFiles
	 * @return \PHPStan\DependencyInjection\Container
	 */
	public function create(array $additionalConfigFiles): Container
	{
		$containerFactory = new ContainerFactory(
			$this->currentWorkingDirectory
		);

		return $containerFactory->create(
			$this->tempDirectory,
			array_merge($this->additionalConfigFiles, $additionalConfigFiles),
			$this->analysedPaths,
			$this->composerAutoloaderProjectPaths,
			$this->analysedPathsFromConfig,
			$this->allCustomConfigFiles,
			$this->usedLevel
		);
	}

}
