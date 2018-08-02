<?php
    /**
     * example.php
     *
     * Example file to show how Articles are used
     *
     * @package    ViroCMS
     * @author     Alex White (https://github.com/ialexpw)
     * @copyright  2018 ViroCMS
     * @license    https://github.com/ialexpw/ViroCMS/blob/master/LICENSE  MIT License
     * @link       https://viro.app
     */
    include 'app/viro.app.php';

    /**
     * Will return the latest article in JSON format
     */
    echo Viro::Article(1);
?>