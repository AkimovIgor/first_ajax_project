<?php

/**
 * Простая функция отладки
 *
 * @param [mixed] $variable
 * @param boolean $die
 * @return void
 */
function dd($variable, $die = true) {
    echo "<pre>";
    print_r($variable);
    echo "</pre>";

    if ($die) die;
}


/**
 * Пагинатор
 *
 * @param [type] $pdo
 * @return array
 */
function paginator($pdo) {

    $paginator = [];
    // кол-во ссылок по правую и левую сторону от активной
    $numLinks = 2;
    // получаем текущую страницу
    $paginator['currentPage'] = isset($_GET['page']) ? $_GET['page'] <= 0 ? 1 : $_GET['page'] : 1;
    // кол-во записей на одной странице
    $paginator['perPage'] = 4;
    // смещение для запроса в бд
    $paginator['offset'] = ($paginator['perPage'] * $paginator['currentPage']) - $paginator['perPage'];
    // префикс для ссылки
    $paginator['link'] = '/?page=';
    // получить все комменты для пагинации со смещением
    $paginator['comments'] = getAllComsForPaginate($pdo, $paginator['offset'], $paginator['perPage']);
    // полусить кол-во всех комментов в бд
    $paginator['commentsCount'] = count(getAllComments($pdo));
    // получить кол-во страниц
    $paginator['pageCount'] = ceil($paginator['commentsCount'] / $paginator['perPage']);
    // стартовое значения для цикла вывода комментов
    $paginator['start'] = (($paginator['currentPage'] - $numLinks) > 0) ? $paginator['currentPage'] - $numLinks : 1;
    // конечное значения для цикла вывода комментов
    $paginator['end'] = (($paginator['currentPage'] + $numLinks) < $paginator['pageCount']) ? $paginator['currentPage'] + $numLinks : $paginator['pageCount'];

    return $paginator;
}
