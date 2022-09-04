<?php 
    require_once 'classes/anuncios.class.php';
    require_once 'classes/usuarios.class.php';
    require_once 'classes/categorias.class.php';

    require 'pages/header.php'; 

    $anuncios = new Anuncios();
    $usuarios = new Usuarios();
    $categorias = new Categorias();

    $filtros = array(
        'categoria' => '',
        'precos' => '',
        'estado' => ''
    );

    if(isset($_GET['filtros']))
    {
        $filtros = $_GET['filtros'];
    }

    $total_anuncios = $anuncios->getTotalAnuncios($filtros);
    $total_usuarios = $usuarios->getTotalUsuarios();
    

    $pagina_atual = 1;
    if(isset($_GET['pagina_atual']) && !empty($_GET['pagina_atual']))
    {
        $pagina_atual = addslashes($_GET['pagina_atual']);
    }

    $anuncios_por_pagina = 2;
    $total_paginas = ceil($total_anuncios / $anuncios_por_pagina);

    $ultimos_anuncios = $anuncios->getUltimosAnuncios($pagina_atual, $anuncios_por_pagina, $filtros);
?>

    <div class="container-fluid">
        <div class="jumbotron">
            <h2>Nós temos hoje <?php echo $total_anuncios; ?> anúncios.</h2>
            <p>E mais de <?php echo $total_usuarios; ?> usuários cadastrados.</p>    
        </div>

        <div class="row">
            <div class="col-sm-3">
                <h4>Pesquisas Avançadas</h4>

                <form method="GET">

                    <div class="form-group">
                        <label for="categorias">Categorias:</label>
                        <select id="categorias" name="filtros[categoria]">
                            <option></option>
                            <?php
                                $lista_categorias = $categorias->getLista(); 
                                foreach($lista_categorias as $categoria): 
                            ?>
                                    <option 
                                        value="<?php echo $categoria['id']; ?>"
                                        <?php echo ($categoria['id'] == $filtros['categoria'])?'selected="selected"':''; ?> 
                                    >
                                        <?php echo $categoria['nome']; ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="precos">Preços:</label>
                        <select id="precos" name="filtros[precos]">
                            <option></option>
                            <option value="0-50" <?php echo  ($filtros['precos']=='0-50')?'selected="selected"':''; ?>>R$ 0 - 50</option>
                            <option value="51-100" <?php echo  ($filtros['precos']=='51-100')?'selected="selected"':''; ?>>R$ 51 - 100</option>
                            <option value="101-200" <?php echo  ($filtros['precos']=='101-200')?'selected="selected"':''; ?>>R$ 101 - 200</option>
                            <option value="201-500" <?php echo  ($filtros['precos']=='201-500')?'selected="selected"':''; ?>>R$ 201 - 500</option>
                            <option value="501-1000" <?php echo  ($filtros['precos']=='501-1000')?'selected="selected"':''; ?>>R$ 501 - 1000</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado de Conservação:</label>
                        <select id="estado" name="filtros[estado]">
                            <option></option>
                            <option value="0" <?php echo  ($filtros['estado']=='0')?'selected="selected"':''; ?>>Ruim</option>
                            <option value="1" <?php echo  ($filtros['estado']=='1')?'selected="selected"':''; ?>>Bom</option>
                            <option value="2" <?php echo  ($filtros['estado']=='2')?'selected="selected"':''; ?>>Ótimo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-info" value="Buscar" />
                    </div>
                </form>
            </div>
            <div class="col-sm-9">
                <h4>Últimos Anúncios</h4>

                <table class="table table-striped">
                    <tbody>
                        <?php
                        if(!empty($ultimos_anuncios)):
                            foreach($ultimos_anuncios as $anuncio): 
                        ?>
                            <tr>
                                <td>
                                    <?php if(!empty($anuncio['url_foto'])): ?>
                                        <img src="assets/imagens/anuncios/<?php echo $anuncio['url_foto']; ?>" height="50" />
                                    <?php else: ?>
                                        <img src="assets/imagens/anuncios/default.jpg" height="50" border="0" />
                                    <?php endif; ?> 
                                </td>
                                <td>
                                    <a href="produto.php?id=<?php echo $anuncio['id']; ?>"><?php echo $anuncio['titulo']; ?></a>
                                    <br />
                                    <?php echo $anuncio['categoria']; ?>
                                </td>
                                <td>
                                    R$ <?php echo number_format($anuncio['valor'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                            ?>
                            <tr>
                                <td>Nenhum anúncio foi encontrado</td>
                            </tr>
                            <?php
                        endif;
                        ?>
                    </tbody>
                </table>

                <ul class="pagination">
                    <?php for($i=1; $i <= $total_paginas; $i++): ?>
                        <li class="<?php echo ($pagina_atual==$i)? 'active':''; ?>">
                            <a href="index.php?<?php
                                $parametros = $_GET;
                                $parametros['pagina_atual'] = $i;
                                echo http_build_query($parametros); 
                            ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor;?>
                </ul>
            </div>
        </div>
    </div>

<?php require 'pages/footer.php'; ?>