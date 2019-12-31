<?php
class User {
    private static $_instance;

    public static function instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new User;
        }
        return self::$_instance;
    }

    public function user_data($id) {
        $data = DB::instance()->select_by_sql("SELECT * FROM `users` WHERE `id` = ?", array($id));
        if (count($data) === 0) {
            echo 'No User found with this ID';
        } else {
            $result = array_shift($data);
            return $result;
        }
    }

    public function login_validate($username, $password) {
        if (trim($username) === "" || $password === "") {
            $errors[] = '<i class="error">Please fill in your Username and Password</i>';
            echo output_errors($errors);
            return false;
        } else {
            $stmt = DB::instance()->select_by_sql("SELECT * FROM `users` WHERE `username` = ?", array($username));
            if (count($stmt) === 0) {
                $errors[] = '<i class="error">This user does not exist</i>';
                echo output_errors($errors);
                return false;
            } else {
                $result = array_shift($stmt);
                $verify = password_verify($password, $result->password);
                if ($verify === false) {
                    $errors[] = '<i class="error">Invalid Username and Password combination</i>';
                    echo output_errors($errors);
                    return false;
                } elseif ($verify === true) {
                    $_SESSION['id'] = $result->id;
                    Redirect::to(SITE_ROOT.'/admin/staff.php');
                    return true;
                }
            }
        }
    }

    public function create_user($username, $password) {
        if (trim($username) === "" || $password === "") {
            $errors[] = '<i class="error">Please fill in all fields</i>';
            echo output_errors($errors);
            return false;
        } else {
            $stmt = DB::instance()->select_by_sql("SELECT * FROM `users` WHERE `username` = ?", array($username));
            if (count($stmt) === 1) {
                $errors[] = '<i class="error">This user already exists</i>';
                echo output_errors($errors);
                return false;
            } else {
                if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
                    $errors[] = '<i class="error">Your Username must contain only alphabets and numbers</i>';
                } else {
                    if (preg_match("/^[0-9]/", $username)) {
                        $errors[] = '<i class="error">Your Username cannot start with a number</i>';
                    }
                }
    
                if (strlen($username) > 50) {
                    $errors[] = '<i class="error">Your Username cannot be more than 50 characters</i>';
                }
    
                if (strlen($password) < 5 || strlen($password) > 50) {
                    $errors[] = '<i class="error">Your Password cannot be less than 5 characters and cannot be more than 50 characters</i>';
                }

                if (preg_match("/\\s/", $username)) {
                    $errors[] = '<i class="error">Your Password must not contain white spaces</i>';
                }
    
                if (!preg_match("/[a-z]/", $password)) {
                    $errors[] = '<i class="error">Your Password must contain a small alphabet</i>';
                }
    
                if (!preg_match("/[A-Z]/", $password)) {
                    $errors[] = '<i class="error">Your Password must contain a capital alphabet</i>';
                }
    
                if (!preg_match("/[0-9]/", $password)) {
                    $errors[] = '<i class="error">Your Password must contain a number</i>';
                }
    
                if (!preg_match("/[@#$%&?*_!]/", $password)) {
                    $errors[] = '<i class="error">Your Password must a special character among @, #, $, %, &, ?, *, _, and !</i>';
                }
    
                if (count($errors) >= 1) {
                    echo output_errors($errors);
                    return false;
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    if (DB::instance()->insert('users', array('username', 'password'), array($username, $hashed_password))) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            
        }
    }
}