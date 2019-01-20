<?php
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

//Rota homepage site
$app->get('/', function() { // Define a rota

	$page = new Page();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

//Rota homepage admin
$app->get('/admin', function() { // Define a rota

	User::verifyLogin();

	$page = new PageAdmin();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");

});

//Rota login admin
$app->get('/admin/login', function() { // Define a rota

	$page = new PageAdmin([
		'header'=>false,
		'footer'=>false
	]);		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("login");

});

//Rota pra envio dos dados do login
$app->post("/admin/login", function(){
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

//Rota para logout
$app->get("/admin/logout", function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

//Rota para página de usuários
$app->get("/admin/users", function(){
	User::verifyLogin();
	$users = User::listAll();
	$page = new PageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));
});

//Rota para página de criar usuário
$app->get("/admin/users/create", function(){ //Pra criar a tela
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
});

//Rota para deletar um usuário
$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();

  $user = new User();
  $user->get((int)$iduser);
  $user->delete();
  header("Location: /admin/users");
  exit;
});

//Rota para página alterar usuário
$app->get("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
  $user = new User();
  $user->get((int)$iduser);
	$page = new PageAdmin();
	$page->setTpl("users-update", array(
    'user'=>$user->getValues()
  ));
});

//Para salvar a criação informação no banco de dados
$app->post("/admin/users/create", function(){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"]=(isset($_POST["inadmin"]))?1:0;
	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;
});

//Para alterar no banco de dados
$app->post("/admin/users/:iduser", function($iduser){
	User::verifyLogin();

  $user = new user();
  $_POST["inadmin"]=(isset($_POST["inadmin"]))?1:0;
  $user->get((int)$iduser);
  $user->setData($_POST);
  $user->update();
  header("Location: /admin/users");
  exit;
});

//Rota que redireciona para a página de Esqueci minha senha
$app->get("/admin/forgot", function(){
    $page = new PageAdmin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl("forgot");
});

$app->post("admin/forgot", function(){
  $user = User::getForgot($_POST["email"]);
});

$app->run();

 ?>
