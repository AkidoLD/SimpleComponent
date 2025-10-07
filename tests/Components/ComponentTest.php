<?php

use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentTagIsInvalidException;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase{
    private Component $component;
    public function setUp(): void{
        $this->component = new Component('comp');
    }

    public function testGetAndSetComponentTag(){
        $this->assertEquals('comp', $this->component->getTag());
        $this->assertEquals('div', $this->component->setTag('div')->getTag());
    }

    public function testSetInvalidTagThrowException(){
        $this->expectException(ComponentTagIsInvalidException::class);
        $this->component->setTag("      ");
    }

    public function testAddAttributeMethodeWorkCorrectlty(){
        $this->component->addAttribute('id', '12');
        $this->component->addAttribute('disable');
        $this->assertEquals('12', $this->component->getAttribute('id'));
        $this->assertNull($this->component->getAttribute('disable'));
    }

    public function testAddAttributeWithInvalidKeyThrowException(){
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $this->component->addAttribute("     ");
    }

    public function testGetExistantAttributeWorksCorrectly(){
        $this->component->addAttribute('exist', 'exist');
        $this->assertEquals('exist', $this->component->getAttribute('exist'));

        //Try to get attribute without value
        $this->component->addAttribute('no_value');
        $this->assertNull($this->component->getAttribute('no_value'));
    }

    public function testGetInexistentAttributeThrowException(){
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $this->component->getAttribute('dont_exist');
    }

    public function testSetInexistentAttributeThrowException(){
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $this->component->setAttribute('invalid', 'invalid_value');

        //Try without value
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $this->component->setAttribute('invalid');
    }

    public function testSetExistentAttributeWorks(){
        $this->component->addAttribute("class", "rounded");
        $this->assertEquals('rounded', $this->component->getAttribute('class'));

        $this->component->setAttribute("class", "big");
        $this->assertEquals('big', $this->component->getAttribute('class'));
    }

    public function testAttributeExistMethod(){
        $this->component->addAttribute('exist');
        $this->assertTrue($this->component->attributeExists('exist'));
        $this->assertFalse($this->component->attributeExists("dont_exist"));
    }

    public function testRemoveAttributeWorksCorrectly(){
        $this->component->addAttribute('attrib');
        $this->assertTrue($this->component->attributeExists('attrib'));

        //Try to delete this attribute
        $this->component->removeAttribute('attrib');
        $this->assertFalse($this->component->attributeExists('attrib'));
    }


}