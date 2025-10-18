<?php

/**
 * Usage example
 * 
 * Creating a User-Card with BEMX
 */
require_once __DIR__."/../vendor/autoload.php";
use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Utils\BEM\BemxHelper;

//Define the User-Card Component;
$user_name = "Akido";
$user_name_label = new Component('label')
    ->addContent($user_name)
    ->addClass(BemxHelper::generate('label', 'user-card',[], ['name']));

$user_photo = "/icon.jpeg";
$user_photo_img = new Component('img', false)
    ->addClass(BemxHelper::generate('img', 'user-card'))
    ->setAttribute('src', $user_photo)
    ->setAttribute('alt', 'user image');
$user_status_label = new Component('label')
    ->addContent('Online')
    ->addClass(BemxHelper::generate('label', 'user-card',[], ['status']));
$user_card = new Component('div')->addClass('user-card')
    ->addContents([
        $user_name_label->render(),
        $user_photo_img->render(),
        $user_status_label->render(),
    ]);

echo $user_card;

?>
