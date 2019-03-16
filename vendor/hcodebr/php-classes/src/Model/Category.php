<?php
    namespace Hcode\Model;

    use \Hcode\DB\Sql;
    use \Hcode\Model;

    class Category extends Model{

      public static function listAll(){
          $sql = new Sql();
          return $sql->select("SELECT * FROM tb_categories ORDER BY idcategory");
      }

      public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
          ':idcategory'=>$this->getidcategory(),
          ':descategory'=>$this->getdescategory()
        ));
        $this->setData($results[0]);
        Category::updateFile();
      }

      //Função que carrega um objeto
      public function get($idcategory){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
          ':idcategory'=>$idcategory
        ));
        $this->setData($results[0]);
      }
      public function delete(){
        $sql = new Sql();
        $result = $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
          ':idcategory'=>$this->getidcategory()
        ));
        Category::updateFile();
      }

      //Função que adiciona no template html o código necessário para mostrar uma nova categoria
      public static function updateFile(){
		    $categories = Category::listAll();
		    $html = [];
		    foreach ($categories as $row) {
			    array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		    }
		    file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
      }

      //O bug tá nessa função. Parece que a sql não tá funcionando como deveria
      //Ou o bind prarms está com algum bug.
      //Conferir se o objeto está carregado corretamente

      //O "Bug" é porque faltava colocar o return
      
      public function getProducts($related){
        $sql = new Sql();
        if($related === true){
          $result = $sql->select("SELECT * FROM tb_products WHERE idproduct IN (SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory); ",[
            ':idcategory'=>$this->getidcategory()
          ]);
          return $result;
        }else{
          $result = $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory); ",[
            ':idcategory'=>$this->getidcategory()
          ]);
          return $result;
        }
      }

      public function addProduct($idcategory, $idproduct){
        $sql = new Sql();
        $sql->query("INSERT INTO tb_productscategories values (:idcategory, :idproduct);", [
          ':idcategory'=>$idcategory,
          ':idproduct'=>$idproduct
        ]);        
      }
      public function removeProduct($idcategory, $idproduct){
        $sql = new Sql();
        $sql->query("DELETE FROM tb_productscategories where idcategory = :idcategory and idproduct = :idproduct;", [
          ':idcategory'=>$idcategory,
          ':idproduct'=>$idproduct
        ]);
      }
    }
?>
