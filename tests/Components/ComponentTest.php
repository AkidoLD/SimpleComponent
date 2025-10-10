<?php

use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAriaIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributeIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributesArrayIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentDataIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentTagIsInvalidException;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase{
    private Component $component;
    
    public function setUp(): void{
        $this->component = new Component('comp');
    }

    public function testSetClosedMethodWorksCorrectly(){
        $this->component->setClosed(true);
        $this->assertTrue($this->component->isClosed());
        $this->assertFalse($this->component->setClosed(false)->isClosed());
    }

    public function testIsClosedMethodWorksCorrectly(){
        $comp = new Component('comp', true);
        $this->assertTrue($comp->isClosed());

        $this->assertFalse($comp->setClosed(false)->isClosed());
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
        $this->assertEquals("",$this->component->getAttribute('disable'));
    }

    public function testAddAttributeReplacesExistingAttribute(){
        $this->component->addAttribute('class', 'old');
        $this->component->addAttribute('class', 'new');
        $this->assertEquals('new', $this->component->getAttribute('class'));
    }

    public function testAddAttributeWithInvalidKeyThrowException(){
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $this->component->addAttribute("     ");
    }

    public function testGetExistantAttributeWorksCorrectly(){
        $this->component->addAttribute('exist', 'exist');
        $this->assertEquals('exist', $this->component->getAttribute('exist'));


        $this->component->addAttribute('no_value');
        $this->assertEquals("",$this->component->getAttribute('no_value'));
    }

    public function testGetInexistentAttributeReturnNull(){
        $this->assertNull($this->component->getAttribute('no_exist'));
    }

    public function testSetInexistentAttributeWithValueAddNewAttribute(){
        $comp = new Component('comp');
        $this->assertFalse($comp->attributeExists('id'));
        $comp->setAttribute('id', '12');
        $this->assertTrue($comp->attributeExists('id'));
        $this->assertEquals('12', $comp->getAttribute('id'));
    }

    public function testSetInexistentAttributeWithoutValueAddNewAttributeWithoutValue(){
        $comp = new Component('comp');
        $this->assertFalse($comp->attributeExists('id'));
        $comp->setAttribute('id');
        $this->assertTrue($comp->attributeExists('id'));
        $this->assertEquals('', $comp->getAttribute('id'));
    }

    public function testSetExistentAttributeWorks(){
        $this->component->addAttribute("class", "rounded");
        $this->assertEquals('rounded', $this->component->getAttribute('class'));

        $this->component->setAttribute("class", "big");
        $this->assertEquals('big', $this->component->getAttribute('class'));
    }

    public function testSetAttributeWorksWithNullValue(){
        $this->component->addAttribute('disabled');
        $this->component->setAttribute('disabled', 'true');
        $this->assertEquals('true', $this->component->getAttribute('disabled'));
    }

    public function testAttributeExistMethod(){
        $this->component->addAttribute('exist');
        $this->assertTrue($this->component->attributeExists('exist'));
        $this->assertFalse($this->component->attributeExists("dont_exist"));
    }

    public function testRemoveAttributeWorksCorrectly(){
        $this->component->addAttribute('attrib', 'value');
        $this->assertTrue($this->component->attributeExists('attrib'));

        $value = $this->component->removeAttribute('attrib');
        
        $this->assertEquals('value', $value);
        $this->assertFalse($this->component->attributeExists('attrib'));
    }

    public function testRemoveAttributeWithNoValueWorksCorrectly(){
        $this->component->addAttribute('disabled');
        
        $value = $this->component->removeAttribute('disabled');
        
        $this->assertEquals("",$value);
        $this->assertFalse($this->component->attributeExists('disabled'));
    }

    public function testRemoveInexistentAttributeReturnNull(){
        $comp = new Component('comp');
        $this->assertNull($comp->removeAttribute('inexistent'));
    }

    public function testAddAttributesWorksCorrectly(){
        $comp = new Component('comp');
        $comp->addAttributes(['id' => '12', 'class' => 'round']);
        
        $this->assertEquals('12', $comp->getAttribute('id'));
        $this->assertEquals('round', $comp->getAttribute('class'));
    }

    public function testAddAttributesWithNoValuesWorksCorrectly(){
        $comp = new Component('comp');
        $comp->addAttributes(['disabled' => '', 'checked' => '']);
        
        $this->assertTrue($comp->attributeExists('disabled'));
        $this->assertTrue($comp->attributeExists('checked'));
        $this->assertEmpty($comp->getAttribute('disabled'));
        $this->assertEmpty($comp->getAttribute('checked'));
    }

    public function testAddAttributesThrowsExceptionIfAnyElementIsInvalid(){
        $comp = new Component('comp');
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $comp->addAttributes([12, 'id' => '12']);
    }

    public function testAddAttributesMethodDontModifyTheAttributesIfTheAddingFailed(){
        $comp = new Component('comp');
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $comp->addAttributes(["disable" => '', "id" => "12", 12, 'class' => '']);
        $this->assertFalse($comp->attributeExists('disable'));
        $this->assertFalse($comp->attributeExists('id'));
        $this->assertFalse($comp->attributeExists('class'));
    }

    public function testAddAttributesMethodSetCleanKeyAndValue(){
        $comp = new Component('comp')->addAttributes(['    id' => '   12', '      class   ' => 'round   ', '    disabled' => '       ']);
        $this->assertTrue($comp->attributeExists('id'));
        $this->assertTrue($comp->attributeExists('class'));
        $this->assertTrue($comp->attributeExists('disabled'));
        $this->assertEquals('12', $comp->getAttribute('id'));
        $this->assertEquals('round', $comp->getAttribute('class'));
        $this->assertEquals('', $comp->getAttribute('disabled'));
    }

    public function testGetAttributesReturnsAllAttributes(){
        $this->component->addAttribute('id', '12');
        $this->component->addAttribute('disabled');
        $this->component->addAttribute('class', 'big');
        
        $expected = ['id' => '12', 'disabled' => null, 'class' => 'big'];
        $this->assertEquals($expected, $this->component->getAttributes());
    }

    public function testGetAttributesReturnsEmptyArrayWhenNoAttributes(){
        $comp = new Component('div');
        $this->assertEquals([], $comp->getAttributes());
    }

    public function testCleanAttributeMethodeWorksCorrectly(){
        $key = "     id   ";
        $value = "      21   ";
        Component::cleanAttribute($key, $value);
        $this->assertEquals('id', $key);
        $this->assertEquals('21', $value);
    }

    public function testCleanAttributeWorksOnAttributeWithNoValue(){
        $key = "  id  ";
        $value = '   value   ';
        Component::cleanAttribute($key, $value);
        $this->assertEquals('id', $key);
        $this->assertEquals("value",$value);
    }

    public function testCheckAttributeThrowIfKeyIsNotString() {
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        $key = 12;
        $value = '';
        Component::checkAttribute($key, $value);
    }
    
    public function testCheckAttributeThrowIfKeyIsEmpty() {
        $key = '';
        $value = '';
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        Component::checkAttribute($key, $value);
    }
    
    public function testCheckAttributeThrowIfValueIsNotString() {
        $key = 'id';
        $value = 12;
        $this->expectException(ComponentAttributeIsInvalidException::class);
        Component::checkAttribute($key, $value);
    }
    
    public function testResetAttributesWorks(){
        $comp = new Component()->addAttribute('id', '12');
        $this->assertNotEmpty($comp->getAttributes());
        $this->assertEmpty($comp->resetAttributes()->getAttributes());
    }

    public function testResetAttributesDoNothingIfTheActuelAttributesIsAlreadyEmpty(){
        $comp = new Component();
        $oldAttrib = $comp->getAttributes();
        $this->assertSame($oldAttrib, $comp->resetAttributes()->getAttributes());
    }

    public function testSetAttributesWorks(){
        $comp = new Component()->addAttribute('disabled');
        $this->assertTrue($comp->attributeExists('disabled'));

        $comp->setAttributes(['id' => '12', 'class' => 'round']);
        $this->assertFalse($comp->attributeExists('disabled'));

        $this->assertTrue($comp->attributeExists('id'));
        $this->assertTrue($comp->attributeExists('class'));
        $this->assertEquals('12', $comp->getAttribute('id'));
        $this->assertEquals('round', $comp->getAttribute('class'));
    }

    public function testSetAttributesDoNothingIfItIsTheSameAttributesArray(){
        $comp = new Component();
        $old = $comp->getAttributes();
        $comp->setAttributes($old);
        $this->assertSame($old, $comp->getAttributes());
    }

    public function testSetAttributesThrowWhenTheNewArryIsInvalid(){
        $comp = new Component();
        $this->expectException(ComponentAttributesArrayIsInvalidException::class);
        $this->expectExceptionMessage('Failed to set attributes : Attribute value must be a string');
        $comp->setAttributes(['id' => 12, 'class' => 'round']);

        $this->expectException(ComponentAttributesArrayIsInvalidException::class);
        $this->expectExceptionMessage('Failed to set attributes : This attribute key is not a string');
        $comp->setAttributes([10 => '12', 'class' => 'round']);

        $this->expectException(ComponentAttributesArrayIsInvalidException::class);
        $this->expectExceptionMessage('Failed to set attributes : Attribute key cannot be empty');
        $comp->setAttributes(['' => '12', 'class' => 'round']);
    }

    public function testRenderAttributeReturnEmptyStringIfNotAttributeSet(){
        $comp = new Component('comp');
        $this->assertEquals('',$comp->renderAttributes());
    }

    public function testRenderAttributeReturnACorrectValue(){
        $comp = new Component('comp')->addAttributes(['id' => '10', 'class' => 'round', 'disabled' => '']);
        $expected = 'id="10" class="round" disabled';
        $this->assertEquals($expected, $comp->renderAttributes());
    }

    public function testAddContentWorksCorrectly(){
        $comp = new Component('comp')->addContent('salut');
        $this->assertEquals('salut', $comp->getContents());

        $comp->addContent('voisin');
        $this->assertEquals('salut'.PHP_EOL.'voisin', $comp->getContents());
    
    }

    public function testAddEmptyContentDontChangeTheContent(){
        $comp = new Component('comp')->addContent('     ');
        $this->assertEquals('', $comp->getContents());

    }

    public function testClearContentResetTheContent(){
        $comp = new Component('comp')->addContent('salut');
        $this->assertEquals('salut', $comp->getContents());
        $this->assertEquals('',$comp->clearContents()->getContents());
    }

    public function testAddContentEscapesHtmlSpecialCharacters(){
        $comp = new Component('comp')->addContent('<script>alert("XSS")</script>');
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';
        $this->assertEquals($expected, $comp->getContents());
    }
    
    public function testRenderAttributesEscapesHtmlSpecialCharacters(){
        $comp = new Component('comp')->addAttribute('onclick', 'alert("XSS")');
        $expected = 'onclick="alert(&quot;XSS&quot;)"';
        $this->assertEquals($expected, $comp->renderAttributes());
    }
    public function testCleanAttributeModifiesOriginalVariables(){
        $key = "  id  ";
        $value = "  value  ";
        Component::cleanAttribute($key, $value);

        $this->assertEquals('id', $key);
        $this->assertEquals('value', $value);
    }

    public function testCheckAttributeThrowsIfKeyBecomesEmptyAfterTrim(){
        $key = '       ';
        $value = 'value';
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        Component::checkAttribute($key, $value);
    }

    public function testAddContentWithOnlyWhitespaceDoesNothing(){
        $comp = new Component('comp');
        $comp->addContent('   ');
        $this->assertEquals('', $comp->getContents());
    }

    public function testMethodChainingWorks(){
        $comp = (new Component('div'))
            ->addAttribute('id', '12')
            ->addAttribute('class', 'big')
            ->addContent('Hello');
        
        $this->assertEquals('12', $comp->getAttribute('id'));
        $this->assertEquals('big', $comp->getAttribute('class'));
        $this->assertStringContainsString('Hello', $comp->getContents());
    }

    public function testRenderAComponentWithoutContentIsOnOneLine(){
        $comp = new Component('comp');
        $render = $comp->render();
        $expected = "<comp></comp>";
        $this->assertEquals($expected, $render);
    }

    public function testRenderAComponentWithContentGoToLine(){
        $comp = new Component('comp')->addContent('component');
        $expect = "<comp>\ncomponent\n</comp>";
        $this->assertEquals($expect, $comp->render());
    }

    public function testRenderAComponentWithAttributeWorksCorrectly(){
        $comp = new Component('comp')->addAttribute('id', '13');
        $expect = "<comp id=\"13\"></comp>";
        $this->assertEquals($expect, $comp->render());
    }

    public function testRenderAComponentWithClosedIsFalse(){
        $comp = new Component('comp', false);
        $expect = "<comp>";
        $this->assertEquals($expect, $comp->render());
    }
    public function testRenderAComponentCompleteComponentWorksCorrectly(){
        $comp = new Component('comp')
            ->addAttribute('id', '10')
            ->addAttribute('class', 'round')
            ->addContent('Component')
            ->addContent('hello');
        $expect = '<comp id="10" class="round">'.PHP_EOL.'Component'.PHP_EOL.'hello'.PHP_EOL.'</comp>';
        $this->assertEquals($expect,$comp->render());
    }

    public function testRenderAComponentAutoClosingIgnoresContent(){
        $comp = new Component('comp', false)
            ->addContent('This should not appear');
        $expect = "<comp>";
        $this->assertEquals($expect, $comp->render());
    }

    public function testSetIdAddIdAttribute(){
        $comp = new Component('comp')->setId('12');
        $this->assertTrue($comp->attributeExists('id'));
    }

    public function testSetIdModifyTheAttributValueIfItIsSet(){
        $comp = new Component('comp')->setId('10');
        $this->assertEquals('10', $comp->getId());
        $this->assertEquals('12', $comp->setId('12')->getId());
    }

    public function testSetIdSetTheCorrectId(){
        $comp = new Component('comp')->setId('12');
        $this->assertEquals('12', $comp->getId());
    }

    public function testGetIdReturnTheCorrectValue(){
        $comp = new Component('comp')->setId('12');
        $this->assertEquals('12', $comp->getId());
    }

    public function testGetIdReturnNullIfIdIsNotSet(){
        $this->assertNull((new Component('comp')->getId()));
    }

    public function testSetClassAddClassAttributeIfNotSet(){
        $comp = new Component();
        $this->assertFalse($comp->attributeExists('class'));
        $comp->setClass('round');
        $this->assertTrue($comp->attributeExists('class'));
    }

    public function testSetClassOverwriteTheValueOfClassAttribute(){
        $comp = new Component()->setClass('round');
        $this->assertEquals('round', $comp->getAttribute('class'));
        $comp->setClass('square');
        $this->assertEquals('square', $comp->getAttribute('class'));
    }

    public function testAddClassSetClassAttributeIfNotSet(){
        $comp = new Component();
        $this->assertFalse($comp->attributeExists('class'));
        $comp->addClass('round');
        $this->assertTrue($comp->attributeExists('class'));
    }

    public function testAddClassDontOverwriteThePreviewValue(){
        $comp = new Component();
        $comp->addClass('round')->addClass('square');
        $this->assertEquals('round square', $comp->getAttribute('class'));
    }
    public function testGetClassReturnNullIfClassIsNotSet(){
        $comp = new Component();
        $this->assertNull($comp->getClass());
    }

    public function testGetClassReturnTheCorrectClassWhenItIsSet(){
        $comp = new Component()->addAttribute('class', 'round');
        $this->assertEquals('round', $comp->getClass());
    }

    public function testHasClassReturnFalseIfClassIsNotSet(){
        $this->assertFalse((new Component())->hasClass('round'));
    }

    public function testHasClassReturnFalseIfTheClassToFoundIsEmpty(){
        $this->assertFalse((new Component())->hasClass(''));
    }

    public function testHasClassReturnTrueIfTheClassIsPresentOnClassAttribute(){
        $comp = new Component()->addClass('round square large');
        $this->assertTrue($comp->hasClass('square'));
    }

    public function testRemoveClassCantRemoveClass(){
        $comp = new Component()->addClass('square round oval');
        $expect = 'square oval';
        $this->assertEquals($expect, $comp->removeClass('round')->getClass());
    }

    public function testRemoveClassRemoveTheClassAttributIfTheClassListIsEmpty(){
        $comp = new Component()->addClass('round');
        $this->assertTrue($comp->attributeExists('class'));
        $comp->removeClass('round');
        $this->assertFalse($comp->attributeExists('class'));
    }

    public function testSetDataAddCorrectDataNameAndValue(){
        $comp = new Component()->setData('name', 'akido');
        $this->assertTrue($comp->attributeExists('data-name'));
        $this->assertEquals('akido', $comp->getAttribute('data-name'));
    }
    
    public function testSetDataAddCorrectData(){
        $comp = new Component()->setData('age', '19');
        $this->assertTrue($comp->attributeExists('data-age'));
        $this->assertEquals('19', $comp->getAttribute('data-age'));
    }
    
    public function testSetDataParametersBeforeUseIt(){
        $comp = new Component()->setData('     name   ', '   akido     ');
        $this->assertTrue($comp->attributeExists('data-name'));
        $this->assertEquals('akido', $comp->getAttribute('data-name'));
    }

    public function testSetDataThrowExceptionIfDataNameIsEmpty(){
        $this->expectException(ComponentDataIsInvalidException::class);
        $comp = new Component()->setData('', '12');
    }

    public function testSetDataThrowExceptionIfValueIsEmpty(){
        $this->expectException(ComponentDataIsInvalidException::class);
        $comp = new Component()->setData('uuid', '');
    }

    public function testGetDataReturnCorrectData(){
        $comp = new Component()->setData('name', 'akido');
        $this->assertEquals('akido', $comp->getData('name'));
    }

    public function testGetDataReturnNullIfDataIsNotFound(){
        $comp = new Component();
        $this->assertNull($comp->getData('no_exist'));
    }

    public function testGetDataReturnNullIfTheDataNameIsEmpty(){
        $this->assertNull(new Component()->getData(''));
    }

    public function testRemoveDataWorks(){
        $comp = new Component()->setData('name', 'alex');
        $this->assertTrue($comp->attributeExists('data-name'));
        $comp->removeData('name');
        $this->assertFalse($comp->attributeExists('data-name'));
    }

    public function testRemoveDataReturnTheRemovedDataValue(){
        $comp = new Component()->setData('name', 'alex');
        $this->assertEquals('alex', $comp->removeData('name'));
    }

    public function testRemoveDataReturnNullIfTheDataNotSet(){
        $this->assertNull(new Component()->removeData('inexist'));
    }

    public function testRemoveDataReturnNullIfDataNameIsEmpty(){
        $this->assertNull(new Component()->removeData(''));
    }

    public function testSetAriaWorks(){
        $comp = new Component()->setAria('langue', 'francaise');
        $this->assertTrue($comp->attributeExists('aria-langue'));
        $this->assertEquals('francaise', $comp->getAria('langue'));
    }

    public function testSetAriaCleanTheParametreBeforeUseIt(){
        $comp = new Component()->setAria('       stream ', "  true   ");
        $this->assertTrue($comp->attributeExists('aria-stream'));
        $this->assertEquals('true', $comp->getAttribute('aria-stream'));
    }

    public function testSetAriaThrowExceptionWhenAriaNameIsEmpty(){
        $this->expectException(ComponentAriaIsInvalidException::class);
        $this->expectExceptionMessage('An aria name cannot be empty');
        $comp = new Component()->setAria('', 'default');
    }

    public function testSetAriaThrowExceptionWhenAriaValueIsEmpty(){
        $this->expectException(ComponentAriaIsInvalidException::class);
        $this->expectExceptionMessage('An aria value cannot be empty');
        $comp = new Component()->setAria('name', '');
    }

    public function testGetAriaWorks(){
        $comp = new Component()->setAria('langue', 'francais');
        $this->assertTrue($comp->attributeExists('aria-langue'));
        $this->assertEquals('francais', $comp->getAria('langue'));
    }

    public function testGetAriaReturnNullIsAriaNameIsEmpty(){
        $this->assertNull(new Component()->getAria(''));
    }

    public function testGetAriaReturnNullIfTheAriaIsNotSet(){
        $this->assertNull(new Component()->setAria('name', 'alex')->getAria('langue'));
    }

    public function testRemoveAriaWorks(){
        $comp = new Component()->setAria('langue', 'fr');
        $this->assertTrue($comp->attributeExists('aria-langue'));
        $this->assertEquals('fr', $comp->removeAria('langue'));
        $this->assertFalse($comp->attributeExists('aria-langue'));
    }

    public function testRemoveAriaReturnNullIfTheAriaNameIsEmpty(){
        $this->assertNull(new Component()->setAria('langue', 'fr')->removeAria(''));
    }

    public function testRemoveAriaReturnNullIfTheAriaNameIsNotFound(){
        $this->assertNull(new Component()->setAria('langue', 'fr')->removeAria('status'));
    }
}