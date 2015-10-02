<?php

namespace Charcoal\Tests\View;

use \Charcoal\View\ViewableTrait as ViewableTrait;
use \Charcoal\View\AbstractView as AbstractView;

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
        $mock = $this->getMockForTrait('\Charcoal\View\ViewableTrait', [], '', true, true, true, ['create_view']);

        $view_args = [
            'logger'=>[]
        ];
        $view = $this->getMockForAbstractClass('\Charcoal\View\AbstractView', $view_args);
       
        $mock->expects($this->any())
            ->method('create_view')
            ->will($this->returnValue($view));

        $mock->foo = 'bar';
        $this->obj = $mock;
        $this->view = $view;
    }

    public function testSetTemplateEngine()
    {
        $obj = $this->obj;
        $this->assertEquals(AbstractView::DEFAULT_ENGINE, $obj->template_engine());
        $ret = $obj->set_template_engine('php');
        $this->assertSame($ret, $obj);
        $this->assertEquals('php', $obj->template_engine());

        $this->setExpectedException('\InvalidArgumentException');
        $obj->set_template_engine(false);
    }

    public function testSetTemplateIdent()
    {
        $obj = $this->obj;
        $this->assertEquals('', $obj->template_ident());

        $ret = $obj->set_template_ident('foobar');
        $this->assertSame($ret, $obj);
        $this->assertEquals('foobar', $obj->template_ident());

        $this->setExpectedException('\InvalidArgumentException');
        $obj->set_template_ident(false);
    }

    public function testSetView()
    {
        $obj = $this->obj;
        $view = $this->view;

        $ret = $obj->set_view($view);
        $this->assertSame($ret, $obj);
        $this->assertEquals($view, $obj->view());
    }

    public function testRenderAndDisplay()
    {
        $obj = $this->obj;
        $obj->foo = 'bar';
        $ret = $obj->render('Hello {{foo}}');
        $this->assertEquals('Hello bar', $ret);

        ob_start();
        $obj->display('Hello {{foo}}');
        $ret2 = ob_get_clean();

        $this->assertEquals($ret, $ret2);
    }
}
