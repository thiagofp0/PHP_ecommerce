<?php
    namespace Hcode;
    /* Essa classe vai criar métodos getters e setters para cada classe que for usada no projeto
     * É muito avançado esse nível de programação, deixa o código robusto e inteligente
     */
    class Model{
        private $values=[];

        /* Esse método vai criar métodos getters e setters para cada classe que for usada no projeto
         * É muito avançado esse nível de programação, deixa o código robusto e inteligente
         */
        public function __call($name, $args){
            $method = substr($name, 0, 3);
            $fieldname = substr($name, 3, strlen($name));
            switch($method){
                case "get":
                    return $this->values[$fieldname];
                break;

                case "set":
                    $this->values[$fieldname] = $args[0];
                break;
            }
        }
        public function setData($data=array()){
            foreach ($data as $key => $value) {
                $this->{"set". $key}($value);
            }
        }

        // Retorna os atributos de uma classe em forma de array
        public function getValues(){
            return $this->values;
        }
    }
?>
