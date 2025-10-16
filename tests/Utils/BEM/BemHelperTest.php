<?php

use AkidoLd\SimpleComponent\Exceptions\BEM\BemHelperException;
use AkidoLd\SimpleComponent\Utils\BEM\BemHelper;
use PHPUnit\Framework\TestCase;

class BemHelperTest extends TestCase
{
    public function testGenerateMethodReturnCorrectBem()
    {
        $expect = 'btn btn--primary';
        $this->assertEquals($expect, BemHelper::generate('btn', null, ['primary']));
    }

    public function testGenerateBemWithElementReturnCorrectBem()
    {
        $expect = 'btn__label btn__label--primary btn__label--large';
        $this->assertEquals($expect, BemHelper::generate('btn', 'label', ['primary', 'large']));
    }

    public function testGenerateBemWithoutElementAndModifier()
    {
        $expect = 'btn';
        $this->assertEquals($expect, BemHelper::generate('btn', null));
    }

    public function testGenerateThrowExceptionIfBlockIsEmpty()
    {
        $this->expectException(BemHelperException::class);
        $this->expectExceptionMessage('[BEM] Invalid block name: cannot be empty.');
        BemHelper::generate('');
    }

    public function testGenerateThrowExceptionIfElementIsNotNullAndEmpty()
    {
        $this->expectException(BemHelperException::class);
        $this->expectExceptionMessage('[BEM] Invalid element name: cannot be empty if not null.');
        BemHelper::generate('btn', '');
    }

    public function testGenerateThrowExceptionIfOneModifierIsEmpty()
    {
        $this->expectException(BemHelperException::class);
        $this->expectExceptionMessage('[BEM] Invalid modifier: cannot be empty.');
        BemHelper::generate('btn', null, ['', 'primary']);
    }

    public function testGenerateThrowExceptionIfModifierIsNotString()
    {
        $this->expectException(BemHelperException::class);
        $this->expectExceptionMessage('[BEM] Invalid modifier type: expected string, got array.');
        BemHelper::generate('btn', null, ['large', ['help'], 'primary']);
    }

    public function testGenerateTrimsExtraSpacesInParameters()
    {
        $expect = 'btn__label btn__label--primary btn__label--large';
        $this->assertEquals(
            $expect,
            BemHelper::generate('  btn  ', '  label  ', ['  primary  ', ' large '])
        );
    }
}
