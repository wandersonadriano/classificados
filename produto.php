<?php 
    require 'classes/anuncios.class.php';
    require 'classes/usuarios.class.php';

    require 'pages/header.php'; 

    $anuncios = new Anuncios();
    $usuarios = new Usuarios();

    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        $id = addslashes($_GET['id']);
    }
    else
    {
        ?>
            <script type="text/javascript">
                window.location.href="index.php";
            </script>
        <?php
        exit;
    }

    $info_anuncio = $anuncios->getAnuncio($id);
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5">

                <div class="carousel slide" data-ride="carousel" id="anuncioCarousel">
                    <div class="carousel-inner" role="listbox">
                        <?php foreach($info_anuncio['fotos'] as $chave => $foto): ?>

                            <div class="item <?php echo ($chave == '0')? 'active': ''; ?>">
                                <img src="assets/imagens/anuncios/<?php echo $foto['url']; ?>" />
                            </div>

                        <?php endforeach; ?>
                    </div>
                    <a class="left carousel-control" href="#anuncioCarousel" role="button" data-slide="prev">
                        <span><</span>
                    </a>
                    <a class="right carousel-control" href="#anuncioCarousel" role="button" data-slide="next">
                        <span>></span>
                    </a>
                </div>

            </div>
            <div class="col-sm-7">
                <h1><?php echo $info_anuncio['titulo']; ?></h1>
                <h4><?php echo $info_anuncio['categoria']; ?></h4>
                <p><?php echo $info_anuncio['descricao']; ?></p>
                <br />
                <h3>R$ <?php echo number_format($info_anuncio['valor'], 2, ',', '.'); ?></h3>
                <h4>Telefone: <?php echo $info_anuncio['telefone']; ?></h3>
            </div>
        </div>
    </div>

<?php require 'pages/footer.php'; ?>