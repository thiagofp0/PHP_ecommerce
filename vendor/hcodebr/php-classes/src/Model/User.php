<?php
    namespace Hcode\Model;

    use \Hcode\DB\Sql;
    use \Hcode\Model;
    use \Hcode\Mailer;

    class User extends Model{

        const SESSION = "User";
        const SECRET = "1234567890123456";
        const IV = "1234567890123456";
        const METHOD = "AES-256-CBC";


        //Função que faz o login do usuário
        public static function login($login, $password){

            $sql = new Sql();
            $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
                ':LOGIN'=>$login
            ));
            if (count($results) === 0) {
                throw new \Exception("Usuario inesistente ou senha inválida");
            }
            $data = $results[0];
            if (password_verify($password, $data["despassword"]) === true){
                $user = new User();
                $user->setData($data);
                $_SESSION[User::SESSION] = $user->getValues();
                return $user;
            }else{
                throw new \Exception("Usuario inesistente ou senha inválida");
            }
        }

        /* Função que verifica se há sessão setada e, no caso de login, confere se atende aos requisitos
         * para o acesso ao sistema.
         */
        public static function verifyLogin($inadmin = true){
            if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
            ) {
                header("Location: /admin/login");
                exit;
            }
        }

        //Função que faz o logout do sistema limpando a sessão
        public static function logout(){
            $_SESSION[User::SESSION] = NULL;
        }
        public static function listAll(){
            $sql = new Sql();
            return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.idperson");
        }

        //Função que cadastra um novo usuário no banco de dados
        public function save(){
            $sql = new Sql();

            $results=$sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
                ":desperson"=>$this->getdesperson(),
                ":deslogin"=>$this->getdeslogin(),
                ":despassword"=>$this->getdespassword(),
                ":desemail"=>$this->getdesemail(),
                ":nrphone"=>$this->getnrphone(),
                ":inadmin"=>$this->getinadmin()
            ));
            $this->setData($results[0]);
        }

        //Função que seleciona o usuário de acordo com seu id
        public function get($iduser){
          $sql = new Sql();
          $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
              ':iduser'=>$iduser
            ));
            $this->setData($results[0]);
        }

        //Função que edita os dados do usuário no banco de dados
        public function update(){
          $sql = new Sql();
          $results=$sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
              ":iduser"=>$this->getiduser(),
              ":desperson"=>$this->getdesperson(),
              ":deslogin"=>$this->getdeslogin(),
              ":despassword"=>$this->getdespassword(),
              ":desemail"=>$this->getdesemail(),
              ":nrphone"=>$this->getnrphone(),
              ":inadmin"=>$this->getinadmin()
          ));
          $this->setData($results[0]);
        }

        // Função que deleta o usuário do banco de dados
        public function delete(){
          $sql = new Sql();
          echo "Teste";
		      $sql->query("CALL sp_users_delete(:iduser)", array(
			      ":iduser"=>$this->getiduser()
		      ));
        }

        //Função que recupera a senha de um determinado usuário.
        public static function getForgot($email){
          $sql = new Sql();

          //Tem gambiarra aqui. Falta lançar um erro caso o email não conste no banco
          $results = array();
          $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email;", array(
            ":email"=>$email
          ));
            $data = $results[0];
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
              ':iduser'=>$data["iduser"],
              ':desip'=>$_SERVER["REMOTE_ADDR"]
            ));

            if (count($results2) === 0 ) {
              throw new \Exception("Não foi possível recuperar a senha.");
            }else {

              $dataRecovery = $results2[0];
              $code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], User::METHOD, User::SECRET, 0, User::IV));
              $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
              $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha da Hcode Store", "forgot", array(
                "name"=> $data["desperson"],
                "link"=> $link
             ));
             $mailer->send();
             return $data;
            }
          }

          public static function ValidForgotDecrypt($code){
            base64_decode($code);

            $idrecovery = openssl_decrypt(base64_decode($code), User::METHOD, User::SECRET, 0, User::IV);
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_userspasswordsrecoveries a INNER JOIN tb_users USING(iduser) INNER JOIN tb_persons c USING(idperson)
              WHERE a.idrecovery = :idrecovery
              AND a.dtrecovery IS NULL
              AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();", array(
                ':idrecovery'=>$idrecovery
              ));

              if(count($results) === 0){
                throw new \Exception("Não foi possível recuperar senha!");
              }else {
                return $results[0];
              }
          }

          public static function SetForgotUsed($idrecovery){
            $sql = new Sql();
            $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
              ':idrecovery'=> $idrecovery
            ));
          }

          public function setPassword($password){
            $sql = new Sql();
            $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
              ':password'=>$password,
              ':iduser'=>$this->getiduser()
            ));
          }
        }
?>