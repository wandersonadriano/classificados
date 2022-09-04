<?php
class Categorias
{
    public function getLista()
    {
        global $pdo;
        $array = array();

        $sql = $pdo->query('SELECT * FROM categorias');
        $sql->execute();

        if($sql->rowCount() > 0)
        {
            $array = $sql->fetchAll();
        }  
        return $array;
    }
}