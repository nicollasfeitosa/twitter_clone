<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {
    private $id;
    private $id_user;
    private $tweet;
    private $data;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set ($atributo, $valor) {
        return $this->$atributo = $valor;
    }

    // Salvar
    public function salvar(){

        $query = "INSERT INTO tweets(id_user, tweet) VALUES (:id_user, :tweet)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    // Recuperar
    public function getAll(){
        $query = "
        SELECT 
            t.id, t.id_user, u.nome, t.tweet, to_char(t.data, 'DD/MM/YYYY HH24:MI:SS') as data
        FROM
            tweets as t
            left join usuarios as u on (t.id_user = u.id)
        WHERE 
            t.id_user = :id_user
            or t.id_user in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_user)
        ORDER by 
            t.data DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function RemoverTweet($TweetID){
        $query = "DELETE FROM tweets WHERE id = :tweetid";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':tweetid', $TweetID);
        $stmt->execute();

        return true;
    }
}