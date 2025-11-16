<?php

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Session class
 * Save or read data to the current session
 * 
 * -- Methods --
 * start_session()
 * set()
 * get()
 * auth()
 * logout()
 * is_logged()
 * getUser()
 * pop()
 * all()
 * regenerate()
 * is_admin()
 * -------------
 */

class Session
{
    private $mainKey = "APP";
    private $userKey = "USER";

    // Start the session if not started yet
    public function start_session() : bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return true;
    }

    // Set a session variable in the APP array
    public function set(mixed $keyOrArray, mixed $value = '') : bool
    {
        $this->start_session();

        if (is_array($keyOrArray)) {
            foreach ($keyOrArray as $key => $data) {
                $_SESSION[$this->mainKey][$key] = $data;
            }

            return true;
        }

        $_SESSION[$this->mainKey][$keyOrArray] = $value;

        return true;
    }

    // Get a session variable from the APP array
    public function get(string $key, mixed $default = '') : mixed
    {
        $this->start_session();

        if (isset($_SESSION[$this->mainKey][$key])){
            return $_SESSION[$this->mainKey][$key];
        }

        return $default;
    }

    // Save the user data into the SESSION USER variable
    public function auth(mixed $user_data) : bool
    {
        $this->start_session();

        $_SESSION[$this->userKey] = $user_data;

        return 1;
    }

    // Delete the SESSION USER variable
    public function logout() : bool
    {
        $this->start_session();

        if (isset($_SESSION[$this->userKey]) && !empty($_SESSION[$this->userKey])) {
            unset($_SESSION[$this->userKey]);
        }

        return 1;
    }

    // Check if the SESSION USER variable is set
    public function is_logged() : bool
    {
        $this->start_session();

        if (isset($_SESSION[$this->userKey]) && !empty($_SESSION[$this->userKey])) {
            return true;
        }

        $tokens = new \Model\Tokens;

        $token = htmlspecialchars($_COOKIE['remember_me'] ?? '');
        $user = $tokens->find_user($token);
        if ($user) {
            $user['telefone'] = formatPhone($user['telefone']);
            unset($user['senha']);

            $this->regenerate();
            $this->auth($user);
            return true;
        } 

        return false;
    }

    // Check if the SESSION USER variable is set
    public function is_admin() : bool
    {
        $this->start_session();

        if (isset($_SESSION[$this->userKey]['cargo']) && $_SESSION[$this->userKey]['cargo'] === "administrador") {
            return true;
        }

        return false;
    }

    // Get the SESSION USER variable
    public function getUser(string $key = '', mixed $default = '') : mixed
    {
        $this->start_session();

        if (!isset($_SESSION[$this->userKey]) || empty($_SESSION[$this->userKey])) {
            return $default;
        }

        if (empty($key)) {
            return $_SESSION[$this->userKey];
        }

        if (isset($_SESSION[$this->userKey][$key]) && !empty($_SESSION[$this->userKey][$key])) {
            return $_SESSION[$this->userKey][$key];
        }

        return $default;
    }

    // Returns data from a key and deletes it
    public function pop(string $key, mixed $default = '') : mixed
    {
        $this->start_session();

        if (isset($_SESSION[$this->mainKey][$key]) && !empty($_SESSION[$this->mainKey][$key])) {
            $value = $_SESSION[$this->mainKey][$key];
            unset($_SESSION[$this->mainKey][$key]);
            return $value;
        }

        return $default;
    }

    // Returns all data from the APP array
    public function all() : mixed
    {
        $this->start_session();

        if (isset($_SESSION[$this->mainKey])) {
            return $_SESSION[$this->mainKey];
        }

        return [];
    }

    // Regenerate the current session id
    public function regenerate() : bool
    {
        $this->start_session();
        session_regenerate_id();

        return 1;
    }
}