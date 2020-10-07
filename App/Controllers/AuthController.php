<?php 

namespace App\Controllers;

// Recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

    public function autenticar() {
        //receber dados do form
        $dados = filter_input_array(INPUT_POST, $_POST, FILTER_SANITIZE_STRING);

        $usuario = Container::getModel('Usuario');

        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        $result = $usuario->autenticar();

        if ($usuario->__get('id') != '' && $usuario->__get('nome') != '') {

            session_start();

            // $_SESSION['user'] = array(
            //     'id' => $usuario->__get('id'),
            //     'nome' => $usuario->__get('nome')
            // );

            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            header('location: /timeline');
            
        } else {
            header('location: /?login=error');
        }

    }

    public function sair() {
        session_start();
        session_destroy();
        header('Location: /');
    }
}