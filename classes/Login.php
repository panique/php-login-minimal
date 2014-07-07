<?php

/**
 * Class login
 * handles the user's login and logout process
 */
class Login
{
    /**
     * @var object The database connection
     */
    private $db_connection = null;
    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        // create/read session, absolutely necessary
        session_start();

        // check the possible login actions:
        // if user tried to log out (happen when user clicks logout button)
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        // login via post data (if user just submitted a login form)
        elseif (isset($_POST["login"])) {
            $this->dologinWithPostData();
        }
    }

    /**
     * log in with post data
     * just include your database class for $conn
     */
    private function dologinWithPostData() {
        
        // check login form contents
        if (empty($_POST['user_name'])) {
            $this->errors[] = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->errors[] = "Password field was empty.";
        } elseif (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {


                $db   = new database();
                $conn = $db->conn();

                try {

                // escape the POST stuff
                $user_name = $_POST['user_name'];

                $sql = "SELECT user_name, user_email, user_password_hash
                        FROM users
                        WHERE user_name = :user_name OR user_email = :user_email;";

   
                $query = $conn->prepare( $sql );
                                /*** bind the paramaters ***/
                   $query->bindParam(':user_name', $user_name, PDO::PARAM_STR);
                   $query->bindParam(':user_email', $user_name, PDO::PARAM_STR);

                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);

                        // if this user exists
                        if ($row) {

                            // using PHP 5.5's password_verify() function to check if the provided password fits
                            // the hash of that user's password
                            if (password_verify($_POST['user_password'], $row["user_password_hash"])) {

                                // write user data into PHP SESSION (a file on your server)
                                $_SESSION['user_name'] = $result_row->user_name;
                                $_SESSION['user_email'] = $result_row->user_email;
                                $_SESSION['user_login_status'] = 1;

                            } else {
                                $this->errors[] = "Wrong password. Try again.";
                            }
                        } else {
                            $this->errors[] = "This user does not exist.";
                        }


                } catch ( PDOException $e ) {

                    var_dump($e->getMessage());
                    echo 'Caught exception: ', $e->getMessage(), "\n";

                    return false;  

                }

        }
    }
    /**
     * perform the logout
     */
    public function doLogout()
    {
        // delete the session of the user
        $_SESSION = array();
        session_destroy();
        // return a little feeedback message
        $this->messages[] = "You have been logged out.";

    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
            return true;
        }
        // default return
        return false;
    }
}
