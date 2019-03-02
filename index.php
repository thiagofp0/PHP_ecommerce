<?php
session_start();
require_once("vendor/autoload.php");
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
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
$app->get("/admin/users/:iduser/delete", function($iduser) {
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
 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
 		"cost"=>12
 	]);
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
//Rota que envia o formulário com o email para a função na classe user
$app->post("/admin/forgot", function(){
  $user = User::getForgot($_POST["email"]);
	header("Location: /admin/forgot/sent");
	exit;
});

//Rota que chama template de confirmação de envio
$app->get("/admin/forgot/sent", function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");
});

//Rota que redireciona para a página de nova senha
$app->get("/admin/forgot/reset", function(){
	$user = User::ValidForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset", array(
		'name'=>$user["desperson"],
		'code'=>$_GET["code"]
	));
});
//Rota que envia o formulário com a nova senha e salva no banco
$app->post("/admin/forgot/reset", function(){

	$forgot = User::ValidForgotDecrypt($_GET["code"]);
	User::SetForgotUsed($forgot["idrecovery"]);
	$user = new User();
	$user->get((int)$forgot["iduser"]);
	$user->setPassword($_POST["password"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");
});


//Rota que redireiona para página de categorias
$app->get("/admin/categories", function(){
	User::verifyLogin();
	$categories = Category::ListAll();
	$page = new PageAdmin();
	$page->setTpl("categories", [
		'categories'=>$categories
	]);
});

//Rota que redireciona para página de criaação de categoria
$app->get("/admin/categories/create", function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");
});

//Rota que envia os dados do formulário para a criaação da categoria
$app->post("/admin/categories/create", function(){
	User::verifyLogin();
	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});

//Rota que deleta categoria
$app->get("/admin/categories/:idcategory/delete", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();
	header("Location: /admin/categories");
	exit;
});

//Rota que redireciona para a página de edição de categoria

$app->get("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin();
	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);	
});
$app->post("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();	
	header('Location: /admin/categories');
	exit;
});
$app->run();
 ?>

