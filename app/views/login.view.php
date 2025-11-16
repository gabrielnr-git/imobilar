    <?=$this->view('header',['title'=>'Login','css'=>'login'])?>

    <style>
        :root{
            --user_input_img: url('<?=ROOT?>/assets/images/login/User.png');
            --email_input_img: url('<?=ROOT?>/assets/images/login/Email.png');
            --pwd_input_img: url('<?=ROOT?>/assets/images/login/Password.png');
            --check_input_img: url('<?=ROOT?>/assets/images/login/check.png');
        }
    </style>
    <main class="login">
        <form method="post" class="login-form">

            <?php if (message()) : ?>
                <small class="message" style="color: var(--blueColor);"><?=message('',true)?></small>
            <?php endif ; ?>

            <h2>Faça Login</h2>
            <section>
                <input type="text" name="login" class="input_username" placeholder="Nome de Usuário ou Email">
                <div>
                    <input type="password" name="senha" class="input_password" id="input_password_login" placeholder="Senha">
                    <img src="<?=loadImage("assets/images/login/show.png")?>" alt="mostrar-senha" id="pwd_img_login" onclick="togglePassword(this)" draggable="false">
                </div>
            </section>
            <a href="<?=ROOT?>/login/recuperar">Esqueceu a senha?</a>
            
            <small><?=$errors['login'] ?? ""?></small>
            
            <label for="input_checkbox"><input type="checkbox" name="remember" id="input_checkbox" value="1" <?=oldChecked('remember','on')?>>Lembrar de mim</label>
            
            <input type="hidden" name="token" value="<?=$ses->get('token')?>">
            <button type="submit" onclick="showLoading()">Login</button>
            <p>Não possui uma conta? <a href="<?=ROOT?>/cadastro">Cadastre-se</a></p>
        </form>
    </main>
    <section class="overlay">
        <div class="loading">
            <img src="<?=loadImage("assets/images/publicar/loading.gif","none")?>" alt="loading-gif" draggable="false">
        </div>
    </section>
</body>
<script src="<?=ROOT?>/assets/js/login.js"></script>
</html>