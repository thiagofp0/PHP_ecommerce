<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // Define a rota
    
	$page = new Page();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

$app->get('/admin', function() { // Define a rota
	
	User::verifyLogin();

	$page = new PageAdmin();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

$app->get('/admin/login', function() { // Define a rota
    
	$page = new PageAdmin([
		'header'=>false,
		'footer'=>false
	]);		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("login");

});

$app->post("/admin/login", function(){
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

$app->get("/admin/logout", function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

$app->run();

 ?>