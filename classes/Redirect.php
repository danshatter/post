<?php
class Redirect {
    public static function to($destination) {
        header('Location: '.$destination);
        die();
        exit();
    }
}