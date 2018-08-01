<?php
    /**
     * index.php
     *
     * Main templating file, with redirection
     *
     * @package    ViroCMS
     * @author     Alex White (https://github.com/ialexpw)
     * @copyright  2018 ViroCMS
     * @license    https://github.com/ialexpw/ViroCMS/blob/master/LICENSE  MIT License
     * @link       https://viro.app
     */

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include 'app/viro.app.php';
    include 'app/lang/en.php';

    # Logout
    if(Viro::LoggedIn() && isset($_GET['logout'])) {
        session_destroy();
        session_start();
    }

    # Simple templating
    if(Viro::LoggedIn()) {
        if(!isset($_GET['page']) || empty($_GET['page'])) {
            Viro::LoadView('dashboard');
        }else{
            Viro::LoadView($_GET['page']);
        }
    }else{
        Viro::LoadView('login');
    }
?>