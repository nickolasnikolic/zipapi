<?php
require 'vendor/autoload.php';
require 'classes/zipapi.php';

error_reporting(-1);//tell me stuff

$app = new \Slim\Slim();

$app->get('/between/:origin/:destination', function($origin, $destination){
  $zip = new Zip();
  echo json_encode($zip->distance($origin, $destination));
});

$app->get('/around/:zipOrigin/:radius', function($zipOrigin, $radius){
  $zip = new Zip();
  echo json_encode($zip->zipcodesinradius($zipOrigin, $radius));
});

$app->get('/', function(){
  echo 'oops! was that a progressive your browser found! nah, just a concerned citizen.';
});

$app->run();
