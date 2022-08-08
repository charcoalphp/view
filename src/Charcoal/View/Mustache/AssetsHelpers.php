<?php

declare(strict_types=1);

namespace Charcoal\View\Mustache;

// From Mustache
use Mustache_LambdaHelper as LambdaHelper;

/**
 * Mustache helpers for rendering CSS and JavaScript.
 */
class AssetsHelpers implements HelpersInterface
{
    /**
     * A string concatenation of inline `<script>` elements.
     *
     * @var string
     */
    private static $js = '';

    /**
     * An array of `<script>` elements referencing external scripts.
     *
     * @var array
     */
    private static $jsRequirements = [];

    /**
     * A string concatenation of inline `<style>` elements.
     *
     * @var string
     */
    private static $css = '';

    /**
     * An array of `<link>` elements referencing external style sheets.
     *
     * @var array
     */
    private static $cssRequirements = [];

    /**
     * Retrieve the collection of helpers.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'purgeJs' => function () {
                $this->purgeJs();
            },
            'addJs' => function ($js, LambdaHelper $helper) {
                $this->addJs($js, $helper);
            },
            'js' => function () {
                return $this->js();
            },
            'addJsRequirement' => function ($js, LambdaHelper $helper) {
                $this->addJsRequirement($js, $helper);
            },
            'jsRequirements' => function () {
                return $this->jsRequirements();
            },
            'addCss' => function ($css, LambdaHelper $helper) {
                $this->addCss($css, $helper);
            },
            'purgeCss' => function () {
                $this->purgeCss();
            },
            'css' => function () {
                return $this->css();
            },
            'addCssRequirement' => function ($css, LambdaHelper $helper) {
                $this->addCssRequirement($css, $helper);
            },
            'cssRequirements' => function () {
                return $this->cssRequirements();
            },
            'purgeAssets' => function () {
                $this->purgeAssets();
            },
        ];
    }

    /**
     * Empty the JS assets queue.
     *
     * @return void
     */
    public function purgeJs(): void
    {
        self::$js = '';
        self::$jsRequirements = [];
    }

    /**
     * Enqueue (concatenate) inline JavaScript content.
     *
     * Must include `<script>` surrounding element.
     *
     * @param string       $js     The JavaScript to add.
     * @param LambdaHelper $helper For rendering strings in the current context.
     * @return void
     */
    public function addJs(string $js, LambdaHelper $helper = null): void
    {
        if ($helper !== null) {
            $js = $helper->render($js);
        }
        self::$js .= $js;
    }

    /**
     * Get the saved inline JavaScript content and purge the store.
     *
     * @return string
     */
    public function js(): string
    {
        $js = self::$js;
        self::$js = '';
        return $js;
    }

    /**
     * Enqueue an external JavaScript file.
     *
     * Must include `<script>` surrounding element.
     *
     * @param string       $js     The JavaScript to add.
     * @param LambdaHelper $helper For rendering strings in the current context.
     * @return void
     */
    public function addJsRequirement(string $js, LambdaHelper $helper = null): void
    {
        $js  = trim($js);
        $key = md5($js);

        if (!isset(self::$jsRequirements[$key])) {
            if ($helper !== null) {
                $js = $helper->render($js);
            }

            self::$jsRequirements[$key] = $js;
        }
    }

    /**
     * Get the JavaScript requirements and purge the store.
     *
     * @return string
     */
    public function jsRequirements(): string
    {
        $req = implode("\n", self::$jsRequirements);
        self::$jsRequirements = [];
        return $req;
    }

    /**
     * Empty the CSS assets queue.
     *
     * @return void
     */
    public function purgeCss(): void
    {
        self::$css = '';
        self::$cssRequirements = [];
    }

    /**
     * Enqueue (concatenate) inline CSS content.
     *
     * Must include `<style>` surrounding element.
     *
     * @param string       $css    The CSS string to add.
     * @param LambdaHelper $helper For rendering strings in the current context.
     * @return void
     */
    public function addCss(string $css, LambdaHelper $helper = null): void
    {
        if ($helper !== null) {
            $css = $helper->render($css);
        }
        self::$css .= $css;
    }

    /**
     * Get the saved inline CSS content and purge the store.
     *
     * @return string
     */
    public function css(): string
    {
        $css = self::$css;
        self::$css = '';
        return $css;
    }

    /**
     * Enqueue an external CSS file.
     *
     * Must include `<link />` or surrounding `<style>` element.
     *
     * @param string       $css    The CSS requirements.
     * @param LambdaHelper $helper For rendering strings in the current context.
     * @return void
     */
    public function addCssRequirement(string $css, LambdaHelper $helper = null): void
    {
        $css = trim($css);
        $key = md5($css);

        if (!isset(self::$cssRequirements[$key])) {
            if ($helper !== null) {
                $css = $helper->render($css);
            }

            self::$cssRequirements[$key] = $css;
        }
    }

    /**
     * Get the CSS requirements and purge the store.
     *
     * @return string
     */
    public function cssRequirements(): string
    {
        $req = implode("\n", self::$cssRequirements);
        self::$cssRequirements = [];
        return $req;
    }

    /**
     * Empty the all asset queues.
     *
     * @return void
     */
    public function purgeAssets(): void
    {
        $this->purgeJs();
        $this->purgeCss();
    }
}
