<?php
    $header = new \Core\Session;
    $header_notif = new \Model\Notificacoes;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?=ROOT?>/assets/images/header/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?=ROOT?>/assets/css/header.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/css/pager.css">
    <?php if (isset($css)) : ?>
        <link rel="stylesheet" href="<?=ROOT . '/assets/css/' . $css . '.css'?>">
    <?php endif ?>
    <?php if (isset($title)) : ?>
        <title><?=$title?></title>
    <?php endif ?>
</head>

<body>

    <div class="mobile-menu-bar">
        <div class="mobile-menu-logo"><a href="<?=ROOT?>"><img src="<?=ROOT?>/assets/images/header/logo-blue.png" alt="Logotipo"></a></div>
        <div class="mobile-menu-button" onclick="toggleHeaderMenu()"><img src="<?=ROOT?>/assets/images/header/menu.png" alt="Menu"></div>
    </div>
    <div class="mobile-menu-overlay"></div>
    <header class="header-main">
        <section class="header-wrapper">
            <div class="header-logo">
                <a href="<?=ROOT?>"><img src="<?=ROOT?>/assets/images/header/logo-blue.png" alt="logo"></a>
                <div class="header-exit-button" onclick="toggleHeaderMenu()"><img src="<?=ROOT?>/assets/images/header/exit.png" alt="Menu"></div>
            </div>
            <nav class="header-navigation">
                <ul>
                    <li class="header_logged"><img src="<?=ROOT?>/assets/images/header/house.png" alt="home-icon"><a href="<?=ROOT?>/moradias">INICIO</a></li>
                    <?php if ($header->is_logged()) : ?>
                        <li class="header_logged"><img src="<?=ROOT?>/assets/images/header/star.png" alt="favorite-icon"><a href="<?=ROOT?>/favoritos">FAVORITOS</a></li>
                        <li class="header_logged"><img src="<?=ROOT?>/assets/images/header/globe.png" alt="publish-icon"><a href="<?=ROOT?>/publicacoes">MINHAS PUBLICAÇÕES</a></li>
                        <li class="header_logged" id="header_user_logged"><img src="<?=ROOT?>/assets/images/header/user.png" alt="publish-icon"><a href="<?=ROOT?>/perfil">SEU PERFIL</a></li>
                        <li class="header_logged" id="header_user_logged"><img src="<?=ROOT?>/assets/images/header/config.png" alt="publish-icon"><a href="<?=ROOT?>/perfil/edit">CONFIGURAÇÕES DA CONTA</a></li>
                        <li class="header_logged" id="header_user_logged"><img src="<?=ROOT?>/assets/images/header/admin.png" alt="publish-icon"><a href="<?=ROOT?>/admin">PAGINA DO ADMINISTRADOR</a></li>
                        <li class="header_logged" id="header_user_logged"><img src="<?=ROOT?>/assets/images/header/logout.png" alt="publish-icon"><a href="<?=ROOT?>/logout">SAIR DA CONTA</a></li>
                    <?php else : ?>
                        <li class="header_not_logged"><img src="<?=ROOT?>/assets/images/header/star.png" alt="favorite-icon"><a>FAVORITOS</a></li>
                        <li class="header_not_logged"><img src="<?=ROOT?>/assets/images/header/globe.png" alt="publish-icon"><a>MINHAS PUBLICAÇÕES</a></li>
                    <?php endif ; ?>
                </ul>
            </nav>
            <div class="header-icons">
                <?php if ($header->is_logged()) : ?>
                    <div class="header-icons-addnotif">
                        <a href="<?=ROOT?>/publicar"><img src="<?=ROOT?>/assets/images/header/plusBlue.png" alt="add icon"></a>
                        <a href="<?=ROOT?>/notificacoes"><img src="<?=ROOT?>/assets/images/header/bell<?=$header_notif->totalUnread($header->getUser("id_usuario")) > 0 ? "-active" : "" ;?>.png" alt="notification bell"></a>
                    </div>
                    <div class="header-pfp">
                        <section onclick="toggleDropdown()">
                            <span>Olá, <?=esc($header->getUser('nome_completo',$header->getUser('nome_usuario')))?></span>
                            <div id="header-pfp-img">
                                <img src="<?=loadImage($header->getUser('pfp'),'user')?>" alt="user pfp" draggable="false">
                            </div>
                        </section>
                        <nav class="header-dropdown">
                            <ul>
                                <li><a href="<?=ROOT?>/perfil">Seu perfil</a></li>
                                <li><a href="<?=ROOT?>/perfil/edit">Configuraçoes da conta</a></li>
                                <?php if ($header->is_admin()) : ?>
                                    <li><a href="<?=ROOT?>/admin">Pagina de Administrador</a></li>
                                <?php endif ; ?>
                                <li><a href="<?=ROOT?>/logout">Sair da conta</a></li>
                            </ul>
                        </nav>
                    </div>
                <?php else : ?>
                    <ul>
                        <li><a href="<?=ROOT?>/cadastro">CADASTRO</a></li>
                        <li><a href="<?=ROOT?>/login">LOGIN</a></li>
                    </ul>
                <?php endif ; ?>
            </div>
        </section>
    </header>
    <script>
        let headerMenuImg = '<?=ROOT?>/assets/images/header/menu.png';
        let headerExitImg = '<?=ROOT?>/assets/images/header/exit.png';
        let headerLogoBlueImg = '<?=ROOT?>/assets/images/header/logo-blue.png';
        let headerLogoWhiteImg = '<?=ROOT?>/assets/images/header/logo-white.png';
    </script>
    <script src="<?=ROOT?>/assets/js/header.js"></script>
    <?php unset($header); ?>