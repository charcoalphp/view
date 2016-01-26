<?php

namespace Charcoal\Tests\View;

use \Charcoal\View\ViewableTrait;
use \Charcoal\View\AbstractView;
use \Charcoal\View\GenericView;

class ViewableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewableTrait $obj
     */
    private $obj;

    /**
     * @var AbtractView $view
     */
    private $view;


    public function setUp()
    {
        $genericView = new GenericView([
            'logger'=>new \Psr\Log\NullLogger()
        ]);
        $engine = new \Charcoal\View\Mustache\MustacheEngine([
            'logger'=>new \Psr\Log\NullLogger()
        ]);
        $genericView->setEngine($engine);
        $mock = $this->getMockForTrait('\Charcoal\View\ViewableTrait');
        $mock->setView($genericView);
        $mock->foo = 'bar';
        $this->obj = $mock;

    }

    public function testSetTemplateEngine()
    {
        $obj = $this->obj;
        $this->assertEquals(AbstractView::DEFAULT_ENGINE, $obj->templateEngine());
        $ret = $obj->setTemplateEngine('php');
        $this->assertSame($ret, $obj);
        $this->assertEquals('php', $obj->templateEngine());

        $this->setExpectedException('\InvalidArgumentException');
        $obj->setTemplateEngine(false);
    }

    public function testSetTemplateIdent()
    {
        $obj = $this->obj;
        $this->assertEquals('', $obj->templateIdent());

        $ret = $obj->setTemplateIdent('foobar');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foobar', $obj->templateIdent());

        $this->setExpectedException('\InvalidArgumentException');
        $obj->setTemplateIdent(false);
    }

    public function testSetView()
    {
        $obj = $this->obj;

        $view = new GenericView([
            'logger'=>new \Monolog\Logger('charcoal.test')
        ]);

        $ret = $obj->setView($view);
        $this->assertSame($ret, $obj);
        $this->assertEquals($view, $obj->view());
    }

    public function testRenderAndDisplay()
    {
        $obj = $this->obj;
        $obj->foo = 'bar';
        $ret = $obj->renderTemplate('Hello {{foo}}');
        $this->assertEquals('Hello bar', $ret);
    }
}
