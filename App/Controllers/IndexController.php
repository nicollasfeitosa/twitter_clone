<?php 

namespace App\Controllers;

// Recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

    public function index() 
    {
        $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
        $this->render('index');
    }

    public function inscreverse() 
    {
        $this->view->usuario = [
            'nome' => '',
            'email' => '',
            'senha' => ''
        ];

        $this->view->erroCadastro = false;
        $this->render('inscreverse');
    }

    public function registrar() 
    {
        //receber dados do form
        $dados = filter_input_array(INPUT_POST, $_POST, FILTER_SANITIZE_STRING);
        extract($dados);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view->usuario = array('nome' => '', 'email' => '', 'senha' => '');
            $this->view->erroCadastro = true;
            $this->render('inscreverse');
        }

        $email = trim($email);
        $nome = trim($nome);

        if (empty($email) || empty($nome)) {
            $this->view->usuario = array('nome' => '', 'email' => '', 'senha' => '');
            $this->view->erroCadastro = true;
            $this->render('inscreverse');
        }

        $usuario = Container::getModel('Usuario');

        $usuario->__set('nome', $nome);
        $usuario->__set('email', $email);
        $usuario->__set('senha', md5($senha));

        if ($usuario->validarCadastro() && count($usuario->getUserPorEmail()) == 0) { // Valida os campos no Back-end e verifica copia no banco
            // Sucesso
            $usuario->salvar();
            $this->render('cadastro');
            
        } else {
            // Erro
            $dados = filter_input_array(INPUT_POST, $_POST, FILTER_SANITIZE_STRING);
            $this->view->usuario = $dados;

            $this->view->erroCadastro = true;
            $this->render('inscreverse');
        }
    }

}