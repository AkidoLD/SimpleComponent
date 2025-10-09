<?php

use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributeIsInvalidException;
use AkidoLd\SimpleComponent\Exceptions\Component\ComponentAttributKeyIsInvalidException;
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

        //Try to get attribute without value
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
    // Test de la mutation sur cleanAttribute (référence &)
    public function testCleanAttributeModifiesOriginalVariables(){
        $key = "  id  ";
        $value = "  value  ";
        Component::cleanAttribute($key, $value);
        // Les variables originales doivent être modifiées
        $this->assertEquals('id', $key);
        $this->assertEquals('value', $value);
    }

    // Test checkAttribute avec espaces qui deviennent vides
    public function testCheckAttributeThrowsIfKeyBecomesEmptyAfterTrim(){
        $key = '       ';
        $value = 'value';
        $this->expectException(ComponentAttributKeyIsInvalidException::class);
        Component::checkAttribute($key, $value);
    }

    // Test que addContent avec seulement des espaces ne change rien
    public function testAddContentWithOnlyWhitespaceDoesNothing(){
        $comp = new Component('comp');
        $comp->addContent('   ');
        $this->assertEquals('', $comp->getContents());
    }

    // Test du chaînage de méthodes
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
}