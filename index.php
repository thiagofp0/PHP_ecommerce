<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // Define a rota
    
	$page = new Page();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

$app->run();

 ?>