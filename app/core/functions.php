<?php

if (!defined("ROOTPATH")) die("Access Denied");

// Check the required extensions for this app
function checkExtensions() {
    $required_extensions = [
		'gd',
		'mysqli',
		'pdo_mysql',
		'pdo_sqlite',
		'curl',
		'fileinfo',
		'intl',
		'exif',
		'mbstring',
	];

    foreach ($required_extensions as $key => $ext) {
        if (extension_loaded($ext)) {
            unset($required_extensions[$key]);
        }
    }

    if (!empty($required_extensions)) {
        die("Please load these extensions in your php.ini file: <br>".implode("<br>",$required_extensions));
    }
}
checkExtensions();

// Show something in a readable way
function show($stuff){
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}

// Sanitize and escape a string
function esc($str) : string {
    return htmlspecialchars($str);
}

// Redirect to another page
function redirect($path){
    header("Location: " . ROOT . "/" . $path);
}

// Format the a raw phone number to brazilian format (12) 12345-6789
function formatPhone($rawnumber) : mixed {
    if (empty($rawnumber)) {
        return '';
    }

    if (strlen($rawnumber) == 11) {
        $formatedNumber = "(" . substr($rawnumber,0,2) . ") " . substr($rawnumber,2,5) . "-" . substr($rawnumber,7,4);
        return $formatedNumber;
    }

    return '';
}

// Format the a raw cep number to brazilian format 12345-678
function formatCEP($rawnumber) : mixed {
    if (empty($rawnumber)) {
        return '';
    }

    if (strlen($rawnumber) == 8) {
        $formatedCEP = substr($rawnumber,0,5) . "-" . substr($rawnumber,5,8);
        return $formatedCEP;
    }

    return '';
}

function formatPrice($price) {
    $length = strlen($price);
    if ($length > 10) return '';
    
    $price = preg_replace("/\./",",",$price);
    if ($length > 6) {
        $offset = $length - 6;
        $price = substr_replace($price,'.',$offset,0); // 0 to replace to chars (inserting in this way)
    }
    return $price;
}

function formatDate($date){
    $date = explode(" ",$date);

    $ymd = explode("-",$date[0]);

    $date = $ymd[2] . "-" . $ymd[1] . "-" . $ymd[0] . " " . substr($date[1],0,-3);

    return $date;
}

// Load image if not exist load placeholder
function loadImage(mixed $filepath = '', string $type = '') : string{
    $filepath = $filepath ?? "";

    if (file_exists($filepath)) {
        return ROOT . "/" . $filepath;
    }

    if ($type === "user") {
        return ROOT . "/assets/images/user_default.png";
    } elseif ($type == "none") {
        return "";
    } else {
        return ROOT . "/assets/images/placeholder.png";
    }
}

function message(string $msg = '', bool $clear = false) : mixed {
    $session = new \Core\Session;

    if (!empty($msg)) {
        $session->set("message",$msg);
    } else if (!empty($session->get("message"))) {
        $msg = $session->get("message");

        if ($clear) {
            $session->pop("message");
        }

        return $msg;
    }

    return '';
}

function addNotification(string $title, string $content,$uid,$link = null) : bool
    {
        $notif = new \Model\Notificacoes;
        
        $notification = [
            'assunto' => $title,
            'conteudo' => $content,
            'id_usuario' => $uid,
            'link' => $link
        ];
        if ($notif->insert($notification)) {
            return true;
        }
        return false;
    }

// Return the URL variables
function URL(string|int $key) : mixed {
    $url = $_GET['url'] ?? "home";
    $url = explode("/",trim($url,"/"));

    switch ($key) {
        case 'page':
        case 0:
            return $url[0] ?? null;
            break;
        case 'section':
        case 'slug':
        case 1:
            return $url[1] ?? null;
            break;
        case 'id':
        case 2:
            return $url[2] ?? null;
            break;
        case 'other':
        case 3:
            return $url[3] ?? null;
            break;
        default:
            return null;
            break;
    }
}

// Display the old value after refresh
function oldValue(string $key, mixed $default = '') : mixed {
    if (isset($_POST[$key])) {
        if ($_POST[$key] == "") {
            return $default;
        }
        return $_POST[$key];
    } else if (isset($_GET[$key])) {
        if ($_GET[$key] == "") {
            return $default;
        }
        return $_GET[$key];
    }
    return $default;
}

//Display the old checked input after a page refresh
function oldChecked(string $key, mixed $value, string $default = '') : string {
    if (isset($_POST[$key])) {
        if ($_POST[$key] == $value) {
            return " checked ";
        }
    } else if (isset($_GET[$key])) {
        if ($_GET[$key] == $value) {
            return " checked ";
        }
    }
    return $default;
}

// Display the old select input after a page refresh
function oldSelect(string $key, mixed $value, mixed $default = '') : string {
    if (isset($_POST[$key])) {
        if ($_POST[$key] == $value) {
            return " selected ";
        }
    } else if (isset($_GET[$key])) {
        if ($_GET[$key] == $value) {
            return " selected ";
        }
    }
    return $default;
}

function regenerate_id(){
    $ses = new \Core\Session;
    
    $interval = 60 * 30;

    if ($ses->get('last_regeneration') === '') {
        $ses->regenerate();
        $ses->set('last_regeneration',time());
    } else if (time() - $ses->get('last_regeneration') >= $interval) {
        $ses->regenerate();
        $ses->set('last_regeneration',time());
    }
}