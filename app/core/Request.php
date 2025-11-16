<?php 

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Request class
 * Gets and sets data in the POST and GET global variables
 * -- Methods --
 * method()
 * posted()
 * post()
 * files()
 * get()
 * request()
 * all()
 * -------------
 */
class Request
{

    // Return the request method
    public function method() : string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // Check if something is posted
    public function posted() : bool
    {
        if ($this->method() === "POST" && !empty($_POST)) {
            return true;
        }

        return false;
    }

    // Return a POST variable
    public function post(string $key = '', mixed $default = '') : mixed
    {
        if (empty($key)) {
            return $_POST;
        } else if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }

    // Return a GET variable
    public function get(string $key = '', mixed $default = '') : mixed
    {
        if (empty($key)) {
            return $_GET;
        } else if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    // Return a FILES variable
    public function files(string $key = '', mixed $default = '') : mixed
    {
        if (empty($key)) {
            return $_FILES;
        } else if (isset($_FILES[$key])) {
            return $_FILES[$key];
        }

        return $default;
    }

    // Return a REQUEST variable
    public function request(string $key, mixed $default) : mixed
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        return $default;
    }

    // Return the entire REQUEST superglobal
    public function all() : mixed
    {
        return $_REQUEST;
    }
}
