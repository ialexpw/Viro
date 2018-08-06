<?php
    /**
     * viro.app.php
     *
     * Main functions file, contains the main Viro class
     *
     * @package    ViroCMS
     * @author     Alex White (https://github.com/ialexpw)
     * @copyright  2018 ViroCMS
     * @license    https://github.com/ialexpw/ViroCMS/blob/master/LICENSE  MIT License
     * @link       https://viro.app
     */

    # Start the session
    if(!headers_sent()) {
		session_start();
    }

    /**
     * Viro Class
     */
    class Viro {

        /**
         * Viro::Connect()
         * Connect to the SQLite database and return the database query
         */
        public static function Connect() {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
            return $db;
        }

        /**
         * Viro::InstallDatabase()
         * Install the database tables needed for ViroCMS
         */
        public static function InstallDatabase() {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

            $db->query('CREATE TABLE users (
                id integer PRIMARY KEY AUTOINCREMENT,
                username varchar,
                email varchar,
                password varchar,
                read varchar,
                write varchar,
                users varchar,
                tools varchar,
                last_login varchar,
                active integer
            )');
            
            $db->query('CREATE TABLE groups (
                id integer PRIMARY KEY AUTOINCREMENT,
                g_name varchar,
                g_slug varchar,
                g_hash varchar,
                u_id integer,
                created varchar,
                FOREIGN KEY(u_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
            )');
            
            $db->query('CREATE TABLE zones (
                id integer PRIMARY KEY AUTOINCREMENT,
                z_name varchar,
                z_slug varchar,
                z_hash varchar,
                g_id integer,
                z_owner integer,
                created varchar,
                FOREIGN KEY(g_id) REFERENCES groups(id) ON UPDATE CASCADE ON DELETE CASCADE
            )');
            
            $db->query('CREATE TABLE content (
                id integer PRIMARY KEY AUTOINCREMENT,
                content varchar,
                c_hash varchar,
                z_id integer,
                edit_by integer,
                created varchar,
                updated varchar,
                FOREIGN KEY(z_id) REFERENCES zones(id) ON UPDATE CASCADE ON DELETE CASCADE
            )');
            
            $db->query('CREATE TABLE articles (
                id integer PRIMARY KEY AUTOINCREMENT,
                title varchar,
                u_id integer,
                content varchar,
                a_hash varchar,
                created varchar,
                updated varchar,
                published integer,
                FOREIGN KEY(u_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
            )');
            
            $db->query('CREATE TABLE backups (
                id integer PRIMARY KEY AUTOINCREMENT,
                title varchar,
                u_id integer,
                created varchar,
                FOREIGN KEY(u_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
            )');

            $db->close();
        }

        /**
         * Viro::GenerateData()
         * Generate the default data into the database, including the admin user, initial group, zone and content
         */
        public static function GenerateData() {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

            $db->exec('BEGIN');

            # Generate default password hash
            $adUser = password_hash("password", PASSWORD_DEFAULT);

            # Get current time
            $ct = time();

            # Admin user
            $db->query('INSERT INTO "users" ("username", "email", "password", "read", "write", "users", "tools", "last_login", "active")
                        VALUES ("admin", "cms@viro.app", "' . $adUser . '", "on", "on", "on", "on", "0", "1")');

            # Generated group
            $db->query('INSERT INTO "groups" ("g_name", "g_slug", "g_hash", "u_id", "created")
                        VALUES ("Main Group", "main-group", "grphash", "1", "' . $ct . '")');

            # Generated zone
            $db->query('INSERT INTO "zones" ("z_name", "z_slug", "z_hash", "g_id", "z_owner", "created")
                        VALUES ("Header Zone", "header-zone", "znehash", "1", "1", "' . $ct . '")');

            # Generated content
            $db->query('INSERT INTO "content" ("content", "c_hash", "z_id", "edit_by", "created", "updated")
                        VALUES ("Test Content", "conhash", "1", "1", "' . $ct . '", "' . $ct . '")');

            # Generated articles
            $db->query('INSERT INTO "articles" ("title", "u_id", "content", "a_hash", "created", "updated", "published")
                        VALUES ("Article title", "1", "Example article content.", "arthash", "' . $ct . '", "' . $ct . '", "0")');

            $db->exec('COMMIT');

            $db->close();
        }

        /**
         * Viro::Content($content)
         * Taking in the content hash, this function returns the content for that zone
         */
        public static function Content($content) {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

            # SELECT the content
            $getContent = $db->prepare('SELECT * FROM "content" WHERE c_hash = :c_hash');
            $getContent->bindValue(':c_hash', $content);
            $getContentRes = $getContent->execute();

            # Get content
            $getContentRes = $getContentRes->fetchArray(SQLITE3_ASSOC);

            # echo the content
            echo $getContentRes['content'];
        }

        /**
         * Viro::Permission($page)
         * Taking in the page, this checks if the user has permissions to view the area
         */
        public static function Permission($page) {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

            $usrID = $_SESSION['UserID'];

            # SELECT the user
            $getUser = $db->prepare('SELECT * FROM "users" WHERE id = :id');
            $getUser->bindValue(':id', $usrID);
            $getUserRes = $getUser->execute();

            # Get user
            $getUserRes = $getUserRes->fetchArray(SQLITE3_ASSOC);

            # Check permission
            if($getUserRes[$page] == 'on') {
                return true;
            }else{
                return false;
            }
        }

        /**
         * Viro::Article($id)
         * Select the article based on an index, 1 = latest article
         */
        public static function Article($id) {
            $db = new SQLite3('app/db/viro.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

            # Offset if ID > 1
            if($id > 1) {
                $offset = $id-1;
            }else{
                $offset = 0;
            }

            # SELECT the articles
            $getArticle = $db->prepare("SELECT * FROM articles ORDER BY id DESC LIMIT 1 OFFSET $offset");
            $getArticleRes = $getArticle->execute();

            # Get article
            $getArticleRes = $getArticleRes->fetchArray(SQLITE3_ASSOC);

            # SELECT the author
            $getUser = $db->prepare('SELECT * FROM users WHERE id = :id');
            $getUser->bindValue(':id', $getArticleRes['u_id']);
            $getUserRes = $getUser->execute();

            # Get author
            $getUserRes = $getUserRes->fetchArray(SQLITE3_ASSOC);

            # Exists
            if($getUserRes != false) {
                $arArr = array(
                    'id'        => $getArticleRes['id'],
                    'title'     => $getArticleRes['title'],
                    'author'    => $getUserRes['username'],
                    'content'   => htmlentities($getArticleRes['content']),
                    'created'   => $getArticleRes['created'],
                    'updated'   => $getArticleRes['updated']
                );
            }else{
                $arArr = array(
                    'error'     => 'not-found'
                );
            }

            return json_encode($arArr);
        }

        /**
         * Viro::Backup()
         * Function to backup the SQLite database
         */
        public static function Backup() {
            return;
        }

        /**
         * Viro::Restore($id)
         * Function to restore the SQLite database
         */
        public static function Restore($id) {
            return;
        }

        /**
         * Viro::Translate($string, $lang)
         * Taking in the string to translate and the language file, this will replace $string with the translation
         */
        public static function Translate($string, $lang) {
            if(!empty($lang[$string])) {
                echo $lang[$string];
            }else{
                echo 'TRANSLATION-ERROR';
            }
        }

        /**
         * Viro::Clean($string)
         * This will remove special characters and double hyphens from $string
         */
        public static function Clean($string) {
            $string = str_replace(' ', '-', $string);
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
            return preg_replace('/-+/', '-', $string);
        }

        /**
         * Viro::LoggedIn()
         * Check logged in status by looking at the sessions
         */
        public static function LoggedIn() {
            if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username'])) {
				return 0;
			}else{
				return 1;
			}
        }

        /**
         * Viro::LoadView($view)
         * Load a view by including the relevant php file, if not found then load the 404
         */
        public static function LoadView($view) {
            if(file_exists('app/tpl/' . $view . '.php')) {
                include 'app/tpl/' . $view . '.php';
            }else{
                include 'app/tpl/404.php';
            }
        }

        /**
         * Viro::LoadPage($page)
         * Load a page with a header redirect
         */
        public static function LoadPage($page) {
            header("Location: ?page=" . $page);
        }

        /**
         * Viro::Version()
         * Return the current version of ViroCMS
         */
        public static function Version() {
            return "v0.1.1-alpha";
        }

        /**
         * Viro::CheckUpdate()
         * Check if updates are available using Viro::Version() and the website
         */
        public static function CheckUpdate() {
            # Get the current version
            $getVer = file_get_contents('https://viro.app/version.txt');

            # Get our version
            $locVer = explode('-', Viro::Version());
            $locVer = str_replace('v', '', $locVer[0]);

            # true = update available
            if($getVer > $locVer) {
                return true;
            }else{
                return false;
            }
        }

        /**
         * Viro::Installed()
         * Check the installation status
         */
        public static function Installed() {
            if(!file_exists('app/db/viro.db')) {
                return false;
            }else{
                return true;
            }
        }
    }
?>