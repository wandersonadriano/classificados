<?php require 'pages/header.php'; ?>

<?php
    if(empty($_SESSION['cLogin']))
    {
        ?>
            <script type="text/javascript">
                window.location.href="login.php";
            </script>
        <?php
        exit;
    }

    require 'classes/anuncios.class.php';
    $anuncios = new Anuncios();

    if(isset($_POST['titulo']) && !empty($_POST['titulo']))
    {
        $titulo = addslashes($_POST['titulo']);
        $categoria = addslashes($_POST['categoria']);
        $valor = addslashes($_POST['valor']);
        $descricao = addslashes($_POST['descricao']);
        $estado = addslashes($_POST['estado']);

        $anuncios->addAnuncio($titulo, $categoria, $valor, $descricao, $estado);

        ?>
            <div class="alert alert-success">
                Produto adicionado com sucesso!
            </div>
        <?php
    }
?>

<div class="container">
    <h1>Meus Anúncios - Adicionar Anúncio</h1>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <select name="categoria" id="categoria" class="form-control">
                <?php
                    require 'classes/categorias.class.php';
                    $categorias = new Categorias();
                    $listaCategorias = $categorias->getLista();

                    foreach($listaCategorias as $categoria):
                        ?>
                            <option value="<?php echo $categoria['id']?>"><?php echo $categoria['nome']?></option>
                        <?php
                    endforeach;
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" />
        </div>
        <div class="form-group">
            <label for="valor">Valor:</label>
            <input type="text" name="valor" id="valor" class="form-control" />
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="estado">Estado de Conservação:</label>
            <select name="estado" id="estado" class="form-control">
                <option value="0">Ruim</option>
                <option value="1">Bom</option>
                <option value="2">Ótimo</option>
            </select>
        </div>
        
        <input type="submit" value="Adicionar" class="btn btn-default" />
    </form>

</div>

<?php require 'pages/footer.php'; ?>