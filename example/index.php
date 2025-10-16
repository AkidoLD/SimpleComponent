<?php

require_once __DIR__."/../vendor/autoload.php";
use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Utils\BEM\BemHelper;
use AkidoLd\SimpleComponent\Utils\BEM\BemxHelper;

$button = new Component('button')->setClass(BemxHelper::generate('btn', [], 'search-bar'))->addContent('Salut');
$label = new Component('input')
    ->setClass(BemxHelper::generate('label', [], 'search-bar'))
    ->addAttribute('type', 'search')
    ->addAttribute('placeholder', 'Entrer quelque chose')
    ->setId('1');
$search_bar = new Component('search-bar', true)->addContents([$button->render(), $label->render()]);

echo $search_bar;
