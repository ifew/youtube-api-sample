<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'base.php';

session_start();
$lives = list_youtuber_live();
if(count($lives) > 0) {
    foreach($lives as $live) {
        echo '<h2>Live : '.$live['gid'].'</h2><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$live['channel_id'].'?autoplay=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        
    };
}