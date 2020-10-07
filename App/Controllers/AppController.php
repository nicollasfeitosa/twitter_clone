<?php 

namespace App\Controllers;

// Recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
    
    public function timeline(){
        
        $this->validarAuth();

        // Recuperação dos Tweets
        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_user', $_SESSION['id']);
        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');

    }

    public function tweet(){

        $this->validarAuth();

        $tweetText = trim($_POST['tweet']);

        if (empty($tweetText) || strlen($tweetText) <= 1) {
            header('location: /timeline');
            exit;
        }

        $tweet = Container::getModel('Tweet');
        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_user', $_SESSION['id']);

        $tweet->salvar();

        header('location: /timeline');
        
    }

    public function validarAuth() {

        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('location: /?login=error');
        }

    }

    public function quem_seguir(){

        $this->validarAuth();

        $usuarios = array();

        $termo = $_GET['termo'] ?? null;

        if ($termo != null) {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $termo);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('quemSeguir');
    }

    public function acao() {

        $this->validarAuth();

        $termo = $_GET['termo'] ?? null;

        $acao = isset($_GET['acao']) ? $_GET['acao'] : ''; // Follow ou Unfollow
        $id_user_seguindo = isset($_GET['id']) ? $_GET['id'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if ($acao == 'follow') {
            
            if ($usuario->verificaSeguindo($id_user_seguindo)['seguindo'] == 0) {
                $usuario->seguirUsuario($id_user_seguindo);
            }

        } else if ($acao == 'unfollow') {

            if ($usuario->verificaSeguindo($id_user_seguindo)['seguindo'] == 1) {
                $usuario->deixarSeguirUsuario($id_user_seguindo);
            }
            
        }

        header('Location: /quem_seguir?termo='.$termo);
        exit;
    }

    public function remover() {
        $this->validarAuth();

        $tweetID = $_POST['tweetID'];

        $tweet = Container::getModel('Tweet');
        $tweet->RemoverTweet($tweetID);

        header('Location: /timeline');
        exit;
    }

    public function seguindo(){
        $this->validarAuth();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('nome', 'a');
        $usuario->__set('id', $_SESSION['id']);
        $usuarios = $usuario->getAllSeguidores();
        $this->view->usuarios = $usuarios;

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('seguindo');
    }

    public function unfollow(){
        $this->validarAuth();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $id_user_seguindo = isset($_GET['id']) ? $_GET['id'] : '';
        $usuario->deixarSeguirUsuario($id_user_seguindo);

        header('Location: /seguindo');
    }
}