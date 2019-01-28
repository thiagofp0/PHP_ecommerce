<?php
    namespace Hcode\Model;

    use \Hcode\DB\Sql;
    use \Hcode\Model;

    class User extends Model{
      
        const SESSION = "User";
        const SECRET_1 = 1234567890123456;
        const SECRET_2 = 6543210987654321;


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

          $sql->query("CALL sp_users_delete(:iduser)", array(
            ':iduser'=>$this->getiduser()
          ));
        }

        //Função que recupera a senha de um determinado usuário.
        public static function getForgot($email){
          $sql = new Sql();
          $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(
            ':email'=>$email
          ));
          if (count($results === 0)) {
            throw new \Exception("Não foi possível recuperar a senha.");
          }else{
            $data = $results[0];
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
              ':iduser'=>$data["iduser"],
              ':desip'=>$_SERVER["REMOTE_ADDR"]
            ));

            if (count($results2) === 0 ) {
              throw new \Exception("Não foi possível recuperar a senha.");
            }else {

              $dataRecovery = $results2[0];
              base64_encode(openssl_encrypt($dataRecovery["idrecovery"], 'AES-128-CBC', SECRET_1, 0, SECRET_2));
              $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

            }
          }
        }
    }
?>
