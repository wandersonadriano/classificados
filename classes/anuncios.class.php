<?php
require 'anuncios_imagens.class.php';

class Anuncios
{
    public function getMeusAnuncios()
    {
        global $pdo;

        $array = array();
        $sql = $pdo->prepare('SELECT *, 
            (select anuncios_imagens.url from anuncios_imagens where anuncios_imagens.id_anuncio = anuncios.id limit 1) AS url_foto 
            FROM anuncios 
            WHERE id_usuario = :id_usuario');
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $array = $sql->fetchAll();
        }

        return $array;
    }

    public function getTotalAnuncios($filtros)
    {
        global $pdo;

        $filtrosString = array('1=1');

        if(!empty($filtros['categoria']))
        {
            $filtrosString[] = 'anuncios.id_categoria = :id_categoria';
        }
        if(!empty($filtros['precos']))
        {
            $filtrosString[] = 'anuncios.valor BETWEEN :preco1 AND :preco2';
        }
        if($filtros['estado'] > -1)
        {
            $filtrosString[] = 'anuncios.estado = :estado';
        }

        $sql = $pdo->prepare("SELECT COUNT(*) AS total_anuncios FROM anuncios WHERE ".implode(' AND ', $filtrosString));
        
        if(!empty($filtros['categoria']))
        {
            $sql->bindValue(':id_categoria', $filtros['categoria']);
        }
        if(!empty($filtros['precos']))
        {
            $precos = explode('-', $filtros['precos']);
            $sql->bindValue(':preco1', $precos[0]);
            $sql->bindValue('preco2', $precos[1]);
        }
        if($filtros['estado'] > -1)
        {
            $sql->bindValue(':estado', $filtros['estado']);
        }

        $sql->execute();

        $total_anuncios = $sql->fetch();

        return $total_anuncios['total_anuncios'];
    }

    public function getUltimosAnuncios($pagina_atual, $anuncios_por_pagina, $filtros)
    {
        global $pdo;

        $offset = ($pagina_atual - 1) * $anuncios_por_pagina;

        $filtrosString = array('1=1');

        if(!empty($filtros['categoria']))
        {
            $filtrosString[] = 'anuncios.id_categoria = :id_categoria';
        }
        if(!empty($filtros['precos']))
        {
            $filtrosString[] = 'anuncios.valor BETWEEN :preco1 AND :preco2';
        }
        if($filtros['estado'] > -1)
        {
            $filtrosString[] = 'anuncios.estado = :estado';
        }

        $array = array();
        $sql = $pdo->prepare(
                "SELECT *, 
                (select anuncios_imagens.url from anuncios_imagens where anuncios_imagens.id_anuncio = anuncios.id limit 1) AS url_foto,
                (select categorias.nome from categorias where categorias.id = anuncios.id_categoria) AS categoria
                FROM anuncios
                WHERE ".implode(' AND ', $filtrosString)."
                ORDER BY id DESC LIMIT $offset, $anuncios_por_pagina"
        );

        if(!empty($filtros['categoria']))
        {
            $sql->bindValue(':id_categoria', $filtros['categoria']);
        }
        if(!empty($filtros['precos']))
        {
            $precos = explode('-', $filtros['precos']);
            $sql->bindValue(':preco1', $precos[0]);
            $sql->bindValue('preco2', $precos[1]);
        }
        if($filtros['estado'] > -1)
        {
            $sql->bindValue(':estado', $filtros['estado']);
        }

        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $array = $sql->fetchAll();
        }

        return $array;
    }

    public function addAnuncio($titulo, $categoria, $valor, $descricao, $estado)
    {
        global $pdo;

        $sql = $pdo->prepare('INSERT INTO anuncios 
            SET titulo = :titulo, id_categoria = :id_categoria, id_usuario = :id_usuario, 
            descricao = :descricao, valor = :valor, estado = :estado');
        $sql->bindValue(':titulo', $titulo);
        $sql->bindValue(':id_categoria', $categoria);
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->bindValue(':descricao', $descricao);
        $sql->bindValue(':valor', $valor);
        $sql->bindValue(':estado', $estado);
        $sql->execute();
    }

    public function excluirAnuncio($id)
    {
        global $pdo;

       $anunciosImagens = new Anuncios_imagens();
       $anunciosImagens->excluirTodasImagensAnuncio($id);

        $sql = $pdo->prepare('DELETE FROM anuncios WHERE id = :id');
        $sql->bindValue(':id', $id);
        $sql->execute();
    }

    public function getAnuncio($id)
    {
        global $pdo;
        $array = array();

        $sql = $pdo->prepare(
            'SELECT *,
            (select c.nome from categorias as c where c.id = a.id_categoria) AS categoria,
            (select u.telefone from usuarios as u where u.id = a.id_usuario) AS telefone
            FROM anuncios AS a
            WHERE a.id = :id');
        $sql->bindValue(':id', $id);
        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $array = $sql->fetch();

            $anunciosImagens = new Anuncios_imagens();
            $array['fotos'] = $anunciosImagens->getImagem($id);
        }

        return $array;
    }

    public function editAnuncio($titulo, $categoria, $valor, $descricao, $estado, $fotos, $id)
    {
        global $pdo;

        $sql = $pdo->prepare('UPDATE anuncios 
            SET titulo = :titulo, id_categoria = :id_categoria, id_usuario = :id_usuario, 
            descricao = :descricao, valor = :valor, estado = :estado WHERE id = :id');
        $sql->bindValue(':titulo', $titulo);
        $sql->bindValue(':id_categoria', $categoria);
        $sql->bindValue(':id_usuario', $_SESSION['cLogin']);
        $sql->bindValue(':descricao', $descricao);
        $sql->bindValue(':valor', $valor);
        $sql->bindValue(':estado', $estado);
        $sql->bindValue(':id', $id);
        $sql->execute();

        if(count($fotos) > 0)
        {
            for($i=0; $i < count($fotos['tmp_name']); $i++)
            {
                $tipo = $fotos['type'][$i];
                if(in_array($tipo, array('image/jpeg', 'image/png')))
                {   
                    $tmpname = md5(time().rand(0, 9999)).'.jpg';
                    move_uploaded_file($fotos['tmp_name'][$i], 'assets/imagens/anuncios/'.$tmpname); //Salvando um arquivo no servidor

                    //$this->redimensionarImagem($tmpname, $tipo);
                    $anunciosImagens = new Anuncios_imagens();
                    $anunciosImagens->addImagem($tipo, $tmpname, $id);
                }
            }
        }
    }

    /*private function redimensionarImagem($tmpname, $tipo)
    {
        list($width_original_image, $height_original_image) = getimagesize('assets/imagens/anuncios/'.$tmpname);

        $ratio_original_image = $width_original_image/$height_original_image; //Proporções da imagem original

        $width = 500;
        $height= 500;

        if($width/$height > $ratio_original_image)
        {
            $width = $height * $ratio_original_image;
        }
        else
        {
            $height = $width / $ratio_original_image;
        }

        $new_image = imagecreatetruecolor($width, $height);

        if($tipo == 'image/jpeg')
        {
            $original_image = imagecreatejpeg('assets/imagens/anuncios/'.$tmpname);
        }
        elseif($tipo == 'image/png')
        {
            $original_image = imagecreatepng('assets/imagens/anuncios/'.$tmpname);
        }

        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $width, $height, $width_original_image, $height_original_image);
        
        imagejpeg($new_image, 'assets/imagens/anuncios/'.$tmpname, 80);

    }*/

}