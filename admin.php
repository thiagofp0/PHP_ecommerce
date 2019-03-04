<?php
    use \Hcode\PageAdmin;
    use \Hcode\Model\User;

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
?>