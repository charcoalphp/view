<?php

namespace Charcoal\View;

use \InvalidArgumentException;

// Module `charcoal-config` dependencies
use \Charcoal\Config\AbstractConfig;

/**
 * View configuration.
 */
class ViewConfig extends AbstractConfig
{
    /**
     * @var array $templatePath
     */
    private $templatePath = [];

    /**
     * @var array $engines
     */
    private $engines = [];

    /**
     * @var string $defaultEngine
     */
    private $defaultEngine;

    /**
     * @var string $defaultController
     */
    private $defaultController;

    /**
     * @return array
     */
    public function defaults()
    {
        return [
            'template_path' => [],
            'engines' => [
                'mustache' => [

                ],
                'php' => [

                ],
                'php-mustache' => [

                ]
            ],
            'default_engine'=> 'mustache'
        ];
    }

    /**
     * @param array $path
     * @return ViewConfig Chainable
     */
    public function setTemplatePath(array $path)
    {
        $this->templatePath = [];
        foreach ($path as $p) {
            $this->addTemplatePath($p);
        }
        return $this;
    }

    /**
     * @param array $path
     * @throws InvalidArgumentException
     * @return ViewConfig Chainable
     */
    public function addTemplatePath($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException(
                'Template path must be a string'
            );
        }
        $this->templatePath[] = $path;
        return $this;
    }

    /**
     * @return array
     */
    public function templatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param array $engines
     * @return ViewConfig Chainable
     */
    public function setEngines($engines)
    {
        $this->engines = [];
        foreach ($engines as $engineIdent => $engineConfig) {
            $this->addEngine($engineIdent, $engineConfig);
        }
        return $this;
    }

    /**
     * @param string $engineIdent
     * @param array  $engineConfig
     * @return ViewConfig Chainable
     */
    public function addEngine($engineIdent, $engineConfig)
    {
        $this->engines[$engineIdent] = $engineConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function engines()
    {
        return $this->engines;
    }

    /**
     * @param string|null $engineIdent
     * @throws InvalidArgumentException
     * @return array
     */
    public function engine($engineIdent = null)
    {
        if ($engineIdent === null) {
            $engineIdent = $this->defaultEngine();
        }
        if (!is_string($engineIdent)) {
            throw new InvalidArgumentException(
                'Invalid engine identifier (must be a string)'
            );
        }

        if (!isset($this->engines[$engineIdent])) {
            throw new InvalidArgumentException(
                sprintf('No configured engines matching "%s"', $engineIdent)
            );
        }
        return $this->engines[$engineIdent];
    }

    /**
     * @param string $engine
     * @throws InvalidArgumentException
     * @return ViewConfig Chainable
     */
    public function setDefaultEngine($engine)
    {
        if (!is_string($engine)) {
            throw new InvalidArgumentException(
                'Default engine must be a string'
            );
        }
        $this->defaultEngine = $engine;
        return $this;
    }

    /**
     * @return string
     */
    public function defaultEngine()
    {
        return $this->defaultEngine;
    }

    /**
     * @param string $engine
     * @throws InvalidArgumentException
     * @return ViewConfig Chainable
     */
    public function setDefaultController($engine)
    {
        if (!is_string($engine)) {
            throw new InvalidArgumentException(
                'Default engine must be a string'
            );
        }
        $this->defaultEngine = $engine;
        return $this;
    }

    /**
     * @return string
     */
    public function defaultController()
    {
        return $this->defaultEngine;
    }
}
