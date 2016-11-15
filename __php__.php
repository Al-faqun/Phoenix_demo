<?php
    /**
     * Этот скрипт, будучи размещённым в каждой папке, где присутствует код сайта,
     * находит корневую папку сайта (самую глубокую, где есть __php__.php)
     * Это позволяет копировать сайт на разные сервера и не прописывать корень вручную.
     */
    //Ищет файл с таким же названием в родительской папке (по отношению к себе)
    if (file_exists(dirname(__DIR__) . '/__php__.php')) 
        include(dirname(__DIR__) . '/__php__.php');
    //если файл не найден, значит, мы в корневой папке (htdocs / www), задаём её адрес в константе
        else define('DOC_ROOT', __DIR__);
?>