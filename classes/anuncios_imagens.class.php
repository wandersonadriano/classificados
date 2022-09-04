<?php
class Anuncios_imagens
{
    public function addImagem($tipo, $tmpname, $id)
    {
        global $pdo;

        $sql = $pdo->prepare('INSERT INTO anuncios_imagens SET id_anuncio = :id_anuncio, url = :url');
        $sql->bindValue(':id_anuncio', $id);
        $sql->bindValue(':url', $tmpname);
        $sql->execute();
    }

    public function getImagem($id)
    {
        global $pdo;

        $fotos = array();

        $sql = $pdo->prepare('SELECT id, url FROM anuncios_imagens WHERE id_anuncio = :id_anuncio');
        $sql->bindValue(':id_anuncio', $id);
        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $fotos = $sql->fetchAll();
        }

        return $fotos;
    }

    public function excluirFoto($id)
    {
        global $pdo;
        $id_anuncio = 0;

        $sql = $pdo->prepare('SELECT id_anuncio, url FROM anuncios_imagens WHERE id = :id');
        $sql->bindValue(':id', $id);
        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $row = $sql->fetch();
            $id_anuncio = $row['id_anuncio'];

            $sql = $pdo->prepare('DELETE FROM anuncios_imagens WHERE id = :id');
            $sql->bindValue(':id', $id);
            $sql->execute();

            unlink('assets/imagens/anuncios/'.$row['url']);
        }

        return $id_anuncio;
    }

    public function excluirTodasImagensAnuncio($id_anuncio)
    {
        global $pdo;

        $sql = $pdo->prepare('DELETE FROM anuncios_imagens WHERE id_anuncio = :id_anuncio');
        $sql->bindValue(':id_anuncio', $id_anuncio);
        $sql->execute();
    }
}