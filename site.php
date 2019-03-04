<?php
    use \Hcode\Page;
    use \Hcode\Model\Category;
//Rota homepage site
$app->get('/', function() { // Define a rota
	$page = new Page();		//Cria uma pagina de acordo com o conteúdo indicado
	$page->setTpl("index");
});

$app->get("/categories/:idcategory", function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new Page();
	$page->setTpl("category", array(
		'category'=>$category->getValues(),
		'products'=>[]
	));
});
?>