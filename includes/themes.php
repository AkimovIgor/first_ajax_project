<?php

$themeList = [
    'cerulean',
    'cyborg',
    'darkly',
    'lumen',
    'lux',
    'materia',
    'sandstone',
    'simplex',
    'sketchy',
    'slate',
    'spacelab',
    'superhero',
    'united',
    'yeti',
];



if (isset($_SESSION['theme'])) {
    echo '<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/' . $_SESSION['theme'] . '/bootstrap.min.css" rel="stylesheet">';

} else {
    echo '<link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/spacelab/bootstrap.min.css" rel="stylesheet">';
}