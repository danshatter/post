<?php
class DB {
    private $_pdo;
    private static $_instance;

    public function __construct() {
        try {
            $this->_pdo = new PDO(DB_TYPE.':'.'host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
            $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function __destruct() {
        self::$_instance = null;
    }

    public static function instance() {
        if (!isset(self::$_instance)){
            return self::$_instance = new DB;
        }
        return self::$_instance;
    }

    public function get_all_subjects() {
        $sql = "SELECT * FROM `subjects` ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_visible_subjects() {
        $sql = "SELECT * FROM `subjects` WHERE `visible` = '1' ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_subjects_count() {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `subjects`;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            return (int)$data->count;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_pages_count() {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `pages`;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            return (int)$data->count;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_pages() {
        $sql = "SELECT * FROM `pages` ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function get_all_visible_pages() {
        $sql = "SELECT * FROM `pages` WHERE `visible` = '1' ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_subject_pages($id) {
        $sql = "SELECT * FROM `pages` WHERE `subject_id` = '$id' ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_visible_subject_pages($id) {
        $sql = "SELECT * FROM `pages` WHERE `subject_id` = '$id' AND `visible` = '1' ORDER BY `position` ASC;";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_all_subject_pages_count($id) {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `pages` WHERE `subject_id` = '$id';";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            return (int)$data->count;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_page_by_id($id) {
        $sql = "SELECT * FROM `pages` WHERE `id` = '$id';";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function get_subject_by_id($id) {
        $sql = "SELECT * FROM `subjects` WHERE `id` = '$id';";
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute()) {
            $data = $stmt->fetch();
            return $data;
        } else {
            echo 'An error occurred. Please try again later';
        }
    }

    public function main_navigation() {
        $subjects = self::instance()->get_all_visible_subjects();
        if (count($subjects) === 0) {
            echo '<h3 class="no-subject">No Subjects Available</h3>';
            $output = '';
        } else {
            $output = '';
            foreach ($subjects as $subject) {
                $output .= '<h3><a class="subjects" href="'.SITE_ROOT.'/subject.php?id='.urlencode(escape($subject->id)).'">'.$subject->subject.'</a></h3>';    
                if (isset($_GET['id'])) {
                    $output .= '<ul>';
                    $subject_pages = self::instance()->get_all_visible_subject_pages($subject->id);
                    foreach ($subject_pages as $subject_page) {
                        $output .= '<li'.(($_GET['id'] == $subject_page->id) ? ' id="current"' : '').'><a class="page-link" href="'.SITE_ROOT.'/content.php?id='.urlencode(escape($subject_page->id)).'">'.$subject_page->title.'</a></li>';
                    }
                    $output .= '</ul>';
                }
            }
        }
        $output .= '<br/><p class="admin">Don\'t have an account? <a href="'.SITE_ROOT.'/admin/add-user.php" class="page-link">Register</a> with us</p>';
        $output .= '<p class="admin">Are You an administrator? <a href="'.SITE_ROOT.'/admin/login.php" class="page-link">Login</a><p>';
        return $output;
    }

    public function admin_navigation() {
        $subjects = self::instance()->get_all_subjects();
        if (count($subjects) === 0) {
            echo '<h3 class="no-subject">No Subjects Available</h3>';
            $output = '';
        } else {
            $output = '';
            foreach ($subjects as $subject) {
                $output .= '<h3><a class="page-link" href="'.SITE_ROOT.'/admin/edit-subject.php?id='.$subject->id.'">'.$subject->subject.'</a></h3>';    
                $output .= '<ul>';
                $subject_pages = self::instance()->get_all_subject_pages($subject->id);
                foreach ($subject_pages as $subject_page) {
                    $output .= '<li><a class="page-link" href="'.SITE_ROOT.'/admin/edit-page.php?id='.urlencode(escape($subject_page->id)).'">'.$subject_page->title.'</a></li>';
                }
                $output .= '</ul>';
            }
        } 
        $output .= '<br/><p><a href="'.SITE_ROOT.'/admin/add-subject.php" class="page-link">+ Add a subject</a></p>';
        $output .= '<p><a href="'.SITE_ROOT.'/index.php" class="page-link">Go to Public site</a></p>';
        $output .= '<br/><a href="'.SITE_ROOT.'/admin/logout.php" class="admin-link">Logout</a>';
        return $output;
    }

    public function insert($table, $fields = array(), $values = array()) {
        if (count($fields) === count($values)) {
            $sql_field = '`'.implode('`, `', $fields).'`';
            $placeholder_array = array_fill(0, count($fields), '?');
            $placeholder = implode(', ', $placeholder_array);
            $sql = "INSERT INTO `$table` ($sql_field) VALUES ($placeholder);";
            $stmt = $this->_pdo->prepare($sql);
            for ($i=0; $i < count($values); $i++) {
                $j = $i + 1;
                $stmt->bindValue($j, $values[$i]);
            }
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }
        
    }

    public function delete($table, $field, $value) {
        $sql = "DELETE FROM `$table` WHERE `$field` = ?;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->bindValue(1, $value);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }   
    }

    public function update($table, $fields = array(), $values = array(), $locator_field, $locator_value) {
        if (count($fields) === count($values)) {
            $placeholder_array = array_fill(0, count($fields), '?');
            $query_array = array();
            for ($i=0; $i < count($values); $i++) { 
                $query_array[] = "`$fields[$i]` = $placeholder_array[$i]"; 
            }
            $query_string = implode(', ', $query_array);
            $sql = "UPDATE `$table` SET {$query_string} WHERE `{$locator_field}` = ?;";
            $stmt = $this->_pdo->prepare($sql);
            for ($i=0; $i < count($values); $i++) { 
                $j = $i + 1;
                $stmt->bindValue($j, $values[$i]);
            }
            $stmt->bindValue(count($values) + 1, $locator_value);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }  
    }

    public function select_by_sql($sql, $values = array()) {
        $count = substr_count($sql, '?');
        if ($count === count($values)) {
            $stmt = $this->_pdo->prepare($sql);
            if (stripos($sql, 'WHERE') > 4 && $count >= 1) {
                $j = 1;
                foreach ($values as $value) {
                    $stmt->bindValue($j, $value);
                    $j++;
                }
            }
            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                return $result;
            } else {
                echo 'An internal error occurred';
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        } 
        
    }
}

