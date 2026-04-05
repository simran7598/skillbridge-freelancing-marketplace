<?php
define('BASE_URL', '/freelance-marketplace/');

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
?>