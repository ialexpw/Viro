<?php
    $Connect = Viro::Connect();

    # Do we have a group?
    if(isset($_GET['hash']) && !empty($_GET['hash'])) {
        $zneHash = $_GET['hash'];
    }else{
        Viro::LoadPage('content');
    }

    # SELECT Content
    $getContent = $Connect->prepare('SELECT * FROM "content" WHERE z_hash = :z_hash');
    $getContent->bindValue(':z_hash', $zneHash);
    $getContentRes = $getContent->execute();

    echo count($getContentRes);

    # Empty?
    if(empty($getContentRes)) {
        //echo 'woa';
    }
    //print_r($getContentRes->fetchArray(SQLITE3_ASSOC));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="index, follow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Viro - Edit Content</title>

        <!-- Styles -->
        <link rel="stylesheet" href="app/tpl/css/siimple.css">
        <link rel="stylesheet" href="app/tpl/css/all.css">
        <link rel="stylesheet" href="app/tpl/css/summernote-lite.css">
        <style>
            /* Remove link styles from sidebar */
            .siimple-list-item a {
                color: inherit;
                text-decoration: inherit;
            }
        </style>

        <!-- Javascript -->
        <script src="app/tpl/js/jquery-3.2.1.slim.min.js"></script>
        <script src="app/tpl/js/summernote-lite.js"></script>
    </head>

    <body>
        <div class="siimple-navbar siimple-navbar--extra-large siimple-navbar--dark">
            <div class="siimple-navbar-title">ViroCMS</div>
            <div class="siimple--float-right">
                <div class="siimple-navbar-item">Profile</div>
                <div class="siimple-navbar-item">Logout</div>
            </div>
        </div>

        <div class="siimple-jumbotron siimple-jumbotron--extra-large siimple-jumbotron--light">
            <div class="siimple-jumbotron-title">Welcome!</div>
            <div class="siimple-jumbotron-detail">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.  
            </div>
        </div>

        <div class="siimple-content siimple-content--extra-large">
            <div class="siimple-grid">
                <div class="siimple-grid-row">
                    <div class="siimple-grid-col siimple-grid-col--3">
                        <div class="siimple-list siimple-list--hover">
                            <div class="siimple-list-item">
                                <a href="?page=dashboard">
                                    <div class="siimple-list-title">Dashboard <div class="siimple--float-right"><i class="fas fa-home"></i></div></div>
                                </a>
                            </div>
                            <div class="siimple-list-item">
                                <a href="?page=content">
                                    <div class="siimple-list-title">Content <div class="siimple--float-right"><i class="far fa-edit"></i></div></div>
                                </a>
                            </div>
                            <div class="siimple-list-item">
                                <a href="?page=articles">
                                    <div class="siimple-list-title">Articles <div class="siimple--float-right"><i class="far fa-newspaper"></i></div></div>
                                </a>
                            </div>
                            <div class="siimple-list-item">
                                <a href="?page=users">
                                    <div class="siimple-list-title">User management <div class="siimple--float-right"><i class="far fa-user-circle"></i></div></div>
                                </a>
                            </div>
                            <div class="siimple-list-item">
                                <a href="?page=tools">
                                    <div class="siimple-list-title">Backup &amp; restore <div class="siimple--float-right"><i class="fas fa-sync-alt"></i></div></div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="siimple-grid-col siimple-grid-col--9">
                        <!-- Breadcrumb menu -->
                        <div class="siimple-breadcrumb">
                        <div class="siimple-breadcrumb-item">Dashboard</div>
                            <div class="siimple-breadcrumb-item">Content</div>
                            <div class="siimple-breadcrumb-item">Zones</div>
                            <div class="siimple-breadcrumb-item">Edit</div>
                        </div>

                        <!-- Break line -->
                        <div class="siimple-rule"></div>

                        <!-- WYSIWYG -->
                        <div id="summernote"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="siimple-footer siimple-footer--extra-large">
            &copy; 2018 ViroCMS - <?php echo Viro::Version(); ?>.
        </div>
        <script>
            $('#summernote').summernote({
                height: 250,
                tabsize: 4,
            });
        </script>
    </body>
</html>