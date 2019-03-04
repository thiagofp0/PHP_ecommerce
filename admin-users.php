<?php
    use \Hcode\PageAdmin;
    use \Hcode\Model\User;

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


?>