<?php

declare(strict_types=1);

namespace Kaizen\Components\Finder;

use Kaizen\Components\Finder\Exception\DirectoryNotFoundException;
use Kaizen\Components\Finder\Iterator\FilenameFilterIterator;
use Kaizen\Components\Finder\Iterator\PathnameFilterIterator;
use Kaizen\Components\Finder\Iterator\RecursiveDirectoryIterator;
use Kaizen\Components\Finder\Utils\SplFileInfo;

class Finder
{
    /** @var string[] */
    private array $dirs = [];

    /** @var string[] */
    private array $filesNames = [];

    /** @var string[] */
    private array $notFilesNames = [];

    /** @var string[] */
    private array $excludedPath = [];

    /** @var string[] */
    private array $mandatoryPaths = [];

    /**
     * Searches files and directories which match defined rules.
     *
     * @param string|string[] $paths
     *
     * @throws DirectoryNotFoundException
     */
    public function in(array|string $paths): self
    {
        $resolvedDirs = [];

        foreach ((array) $paths as $path) {
            if (is_dir($path)) {
                $resolvedDirs[] = [$path];

                continue;
            }

            if ($glob = glob(
                $path,
                (\defined('GLOB_BRACE') ? \GLOB_BRACE : 0) | \GLOB_ONLYDIR | \GLOB_NOSORT
            )) {
                $resolvedDirs[] = $glob;

                continue;
            }

            throw new DirectoryNotFoundException(sprintf('directory %s not found', $path));
        }

        $this->dirs = array_merge($this->dirs, ...$resolvedDirs);

        return $this;
    }

    /**
     * Add rules that files must match, it can be either a glob patter or a regex.
     *
     * example:
     *
     * $finder->fileName(['*.php', 'src/**\/\{abc, def}*.php']);
     * $finder->fileName(['/*\.php$/', '*.php']);
     * $finder->fileName('file.php');
     *
     * @param string|string[] $filesNames
     */
    public function fileName(array|string $filesNames): self
    {
        $this->filesNames = (array) $filesNames;

        return $this;
    }

    /**
     * @param string|string[] $notFileNames
     */
    public function notFilename(array|string $notFileNames): self
    {
        $this->notFilesNames = array_merge($this->notFilesNames, (array) $notFileNames);

        return $this;
    }

    /**
     * @param string|string[] $dirs
     */
    public function exclude(array|string $dirs): self
    {
        $this->excludedPath = array_merge($this->excludedPath, (array) $dirs);

        return $this;
    }

    /**
     * @param string|string[] $paths
     */
    public function mandatoryPath(array|string $paths): self
    {
        $this->mandatoryPaths = array_merge($this->mandatoryPaths, (array) $paths);

        return $this;
    }

    /**
     * @return \Iterator<string, \SplFileInfo>
     */
    public function getIterator(): \Iterator
    {
        if ([] === $this->dirs) {
            throw new \LogicException('You must call one of in() method before iterating over a Finder.');
        }

        $iterator = new \AppendIterator();

        foreach ($this->dirs as $dir) {
            $iterator->append($this->searchInDirectory($dir));
        }

        return $iterator;
    }

    /**
     * Search recursively into all the directories provided with "in()" method.
     *
     * @return \Iterator<string, SplFileInfo> Return an iterator with all found directories as SplFileInfo object
     */
    private function searchInDirectory(string $dir): \Iterator
    {
        $iterator = new RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($iterator);

        if ([] !== $this->filesNames || [] !== $this->notFilesNames) {
            $iterator = new FilenameFilterIterator($iterator, $this->filesNames, $this->notFilesNames);
        }

        if ([] !== $this->excludedPath || [] !== $this->mandatoryPaths) {
            return new PathnameFilterIterator($iterator, $this->mandatoryPaths, $this->excludedPath);
        }

        return $iterator;
    }
}
