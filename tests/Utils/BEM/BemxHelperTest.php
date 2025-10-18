<?php

use AkidoLd\SimpleComponent\Utils\BEM\BemxHelper;
use AkidoLd\SimpleComponent\Exceptions\BEM\BemxHelperException;
use PHPUnit\Framework\TestCase;

class BemxHelperTest extends TestCase {

    public function testGenerateChildOnly() {
        $expected = 'btn btn--primary';
        $this->assertEquals($expected, BemxHelper::generate('btn',null, ['primary']));
    }

    public function testGenerateWithParent() {
        $expected = 'btn btn--primary menu-item__btn menu-item__btn--expanded';
        $this->assertEquals(
            $expected,
            BemxHelper::generate('btn', 'menu-item', ['primary'], ['expanded'])
        );
    }

    public function testGenerateParentOnlyNoModifiers() {
        $expected = 'btn btn--primary menu-item__btn';
        $this->assertEquals(
            $expected,
            BemxHelper::generate('btn', 'menu-item',['primary'] )
        );
    }

    public function testGenerateCatchBemGenerationExceptionAndThrowBemxExceptionInstead(){
        $this->expectException(BemxHelperException::class);
        $this->expectExceptionMessage('[BEMX] [BEM] Invalid block name: cannot be empty.');
        BemxHelper::generate('');
    }
    
    public function testGenerateThrowsOnEmptyParentBlock() {
        $this->expectException(BemxHelperException::class);
        BemxHelper::generate('btn', '');
    }

    public function testGenerateThrowsOnInvalidParentModifierType() {
        $this->expectException(BemxHelperException::class);
        BemxHelper::generate('btn', 'menu-item',[], [42]);
    }

    public function testGenerateThrowsOnEmptyParentModifier() {
        $this->expectException(BemxHelperException::class);
        BemxHelper::generate('btn', 'menu-item',[], ['']);
    }

    public function testGenerateTrimsParentModifiers() {
        $expected = 'btn btn--primary menu-item__btn menu-item__btn--expanded';
        $this->assertEquals(
            $expected,
            BemxHelper::generate('btn', 'menu-item ', ['primary'], [' expanded '])
        );
    }
}
