<?php
require 'config.php';

if(empty($_SESSION['cLogin']))
{
    header('Location: login.php');
    exit;
}

require 'classes/anuncios_imagens.class.php';
$anunciosImagens = new Anuncios_imagens();

if(isset($_GET['id_imagem']) && !empty($_GET['id_imagem']))
{
    $id_anuncio = $anunciosImagens->excluirFoto($_GET['id_imagem']);
}

if(isset($id_anuncio))
{
    header('Location: editar-anuncio.php?id='.$id_anuncio);
}
else{
    header('Location: meus-anuncios.php');
}