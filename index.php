<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // Define a rota
    
	$page = new Page();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

$app->get('/admin', function() { // Define a rota
    
	$page = new PageAdmin();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

$app->run();

 ?>