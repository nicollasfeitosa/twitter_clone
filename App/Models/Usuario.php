<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set ($atributo, $valor) {
        return $this->$atributo = $valor;
    }

    // Salvar
    public function salvar() {
        $query = "INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha')); // MD5 -> hash de 32 caracteres
        $stmt->execute();

        return $this;
    }

    // Validar se um cadastro pode ser feito
    public function validarCadastro() {
        $valido = true;

        if (strlen($this->__get('nome')) <= 4) {
            $valido = false;
        }

        if (strlen($this->__get('email')) <= 14) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) <= 6) {
            $valido = false;
        }

        return $valido;
    }

    // Recuperar um usuário por e-mail
    public function getUserPorEmail() {
        $query = "SELECT nome, email from usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Autenticar
    public function autenticar() {
        $query = "SELECT id, nome, email FROM usuarios WHERE email = :email and senha = :senha";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario) {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }

        return $this;
    }

    public function getAll(){
        $query = "
            select 
                u.id, u.nome, u.email,
                (
                    select 
                        count(*) from usuarios_seguidores as us
                    where
                        us.id_usuario = :id_user and us.id_usuario_seguindo = u.id
                ) as seguindo
            from 
                usuarios as u
            where 
                LOWER(u.nome) like lOWER(:nome) and u.id != :id_user
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario($id_user_seguindo)
    {
        $query = "INSERT INTO usuarios_seguidores (id_usuario, id_usuario_seguindo) VALUES (:id_usuario, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id')); 
        $stmt->bindValue(':id_usuario_seguindo', $id_user_seguindo); 
        $stmt->execute();

        return true;
    }

    public function deixarSeguirUsuario($id_user_seguindo){
        $query = "DELETE FROM usuarios_seguidores WHERE id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id')); 
        $stmt->bindValue(':id_usuario_seguindo', $id_user_seguindo); 
        $stmt->execute();

        return true;
    }

    // Informações do Usuario
    public function getInfoUsuario() {
        $query = 'SELECT nome FROM usuarios where id= :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Total de Tweets
    public function getTotalTweets() {
        $query = 'SELECT count (*) as total_tweets from tweets where id_user = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Total de usuario que esta seguindo
    public function getTotalSeguindo() {
        $query = 'SELECT count (*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Total de seguidores
    public function getTotalSeguidores() {
        $query = 'SELECT count (*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllSeguidores(){
        $query = '
        SELECT 
        us.id_usuario_seguindo, u.nome
        FROM 
            usuarios_seguidores as us
            left join usuarios as u on (us.id_usuario_seguindo = u.id)
        where 
            us.id_usuario = :id_user
        ';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function verificaSeguindo($id_user_seguindo) {
        $query = 'SELECT count (*) as seguindo from usuarios_seguidores where id_usuario = :id_user and id_usuario_seguindo = :id_seguindo';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $_SESSION['id']);
        $stmt->bindValue(':id_seguindo', $id_user_seguindo);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
}