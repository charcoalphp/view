<?php

namespace Charcoal\View;

// Dependencies from `PHP`
use \InvalidArgumentException;

// Module `charcoal-core` dependencies
use \Charcoal\Log\LoggerAwareInterface;
use \Charcoal\Log\LoggerAwareTrait;

// Module `charcoal-config` dependencies
use \Charcoal\Config\ConfigurableInterface;
use \Charcoal\Config\ConfigurableTrait;

// Local namespace dependencies
use \Charcoal\View\Mustache\MustacheEngine;
use \Charcoal\View\Php\PhpEngine;
use \Charcoal\View\PhpMustache\PhpMustacheEngine;
use \Charcoal\View\Twig\TwigEngine;
use \Charcoal\View\ViewInterface;

/**
 * Base abstract class for _View_ interfaces, implements `ViewInterface`.
 *
 * Also implements the `ConfigurableInterface`
 */
abstract class AbstractView implements
    ConfigurableInterface,
    LoggerAwareInterface,
    ViewInterface
{
    use LoggerAwareTrait;
    use ConfigurableTrait;

    const DEFAULT_ENGINE = 'mustache';

    /**
     * @var string $template_ident
     */
    private $template_ident;

    /**
     * @var string $template
     */
    private $template;

    /**
     * @var string $engine_type
     */
    private $engine_type = self::DEFAULT_ENGINE;

    /**
     * @var EngineInterface $engine
     */
    private $engine;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param array $data
     * @return AbstractView Chainable
     */
    public function set_data(array $data)
    {
        foreach ($data as $prop => $val) {
            $func = [$this, 'set_'.$prop];
            if (is_callable($func)) {
                call_user_func($func, $val);
            } else {
                $this->{$prop} = $val;
            }
        }

        return $this;
    }

    /**
     * > ConfigurableTrait . create_config()
     *
     * @param array $data
     * @return ViewConfig
     */
    public function create_config(array $data = null)
    {
        $config = new ViewConfig();
        if ($data !== null) {
            $config->set_data($data);
        }
        return $config;
    }

    /**
     * Set the engine type
     *
     * @param string $engine_type
     * @throws InvalidArgumentException
     * @return AbstractView Chainable
     */
    public function set_engine_type($engine_type)
    {
        if (!is_string($engine_type)) {
            throw new InvalidArgumentException(
                'Engine type must be a string (mustache, php or php-mustache)'
            );
        }
        $this->engine_type = $engine_type;
        return $this;
    }

    /**
     * @return string
     */
    public function engine_type()
    {
        return $this->engine_type;
    }

    /**
     * @param EngineInterface $engine
     */
    public function set_engine(EngineInterface $engine)
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * @return EngineInterface
     */
    public function engine()
    {
        if ($this->engine === null) {
            $this->engine = $this->create_engine();
        }
        return $this->engine;
    }

    /**
     * @return EngineInterface
     */
    public function create_engine()
    {
        $type = $this->engine_type();
        switch ($type) {
            case 'mustache':
                return new MustacheEngine([
                    'logger' => $this->logger(),
                    'cache'  => null,
                    'loader' => null
                ]);

            case 'php':
                return new PhpEngine([
                    'logger' => $this->logger(),
                    'cache'  => null,
                    'loader' => null
                ]);

            case 'php-mustache':
                return new PhpMustacheEngine([
                    'logger' => $this->logger(),
                    'cache'  => null,
                    'loader' => null
                ]);

            case 'twig':
                return new TwigEngine([
                    'logger' => $this->logger(),
                    'cache'  => null,
                    'loader' => null
                ]);

            default:
                return new MustacheEngine([
                    'logger' => $this->logger(),
                    'cache'  => null,
                    'loader' => null
                ]);
        }
    }

    /**
     * @param string $template_ident
     * @throws InvalidArgumentException if the provided argument is not a string
     * @return AbstractView Chainable
     */
    public function set_template_ident($template_ident)
    {
        if (!is_string($template_ident)) {
            throw new InvalidArgumentException(
                'Template ident must be a string.'
            );
        }

        $this->template_ident = $template_ident;
        return $this;
    }

    /**
     * @return string
     */
    public function template_ident()
    {
        return $this->template_ident;
    }

    /**
     * @param string $template
     * @throws InvalidArgumentException if the provided argument is not a string
     * @return AbstractView Chainable
     */
    public function set_template($template)
    {
        if (!is_string($template)) {
            throw new InvalidArgumentException(
                'Template must be a string.'
            );
        }

        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function template()
    {
        if ($this->template === null) {
            return $this->load_template();
        }

        return $this->template;
    }

    /**
     * @param string $template_ident
     * @throws InvalidArgumentException
     * @return string
     */
    public function load_template($template_ident = null)
    {
        if ($template_ident === null) {
            $template_ident = $this->template_ident();
        }
        if (!is_string($template_ident)) {
            throw new InvalidArgumentException(
                'Template ident must be a string'
            );
        }
        if (!$template_ident) {
            return '';
        }
        $template = $this->engine()->load_template($template_ident);
        return $template;
    }

    /**
     * @param mixed $context
     * @return AbstractView Chainable
     */
    public function set_context($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * @param string $template
     * @param mixed  $context
     * @return string The rendered template
     */
    public function render($template_ident = null, $context = null)
    {
        if ($template_ident === null) {
            $template_ident = $this->template_ident();
        }
        if ($context === null) {
            $context = $this->context();
        }

        return $this->engine()->render($template_ident, $context);
    }

    /**
     * @param string $template_ident
     * @param mixed  $context
     * @return string The rendered template
     */
    public function render_template($template_string=null, $context = null)
    {
        if($template_string === null) {
            $template_string = $this->template();
        }
        return $this->engine()->render($template_string, $context);
    }
}
