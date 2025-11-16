    <?=$this->view('header',['title'=>'Cadastro','css'=>'cadastro'])?>
    <style>
        :root{
            --user_input_img: url('<?=ROOT?>/assets/images/cadastro/User.png');
            --email_input_img: url('<?=ROOT?>/assets/images/cadastro/Email.png');
            --pwd_input_img: url('<?=ROOT?>/assets/images/cadastro/Password.png');
        }
    </style>
    <main class="register">
        <form method="post" class="register-form">

            <?php if (message()) : ?>
                <small class="message" style="color: var(--blueColor);"><?=message('',true)?></small>
            <?php endif ; ?>

            <h2>Cadastre-se</h2>
            <section>
                <div>
                    <input type="text" name="nome_usuario" class="input_username" placeholder="Nome de Usuário" value="<?=oldValue('nome_usuario')?>">
                    <small><?=$errors['nome_usuario'] ?? ""?></small>
                </div>
                <div>
                    <input type="email" name="email" class="input_email" placeholder="E-mail" value="<?=oldValue('email')?>">
                    <small><?=$errors['email'] ?? ""?></small>
                </div>
                <div>
                    <div>
                        <input type="password" name="senha" class="input_password" id="input_password_register" placeholder="Senha">
                        <img src="<?=loadImage("assets/images/cadastro/show.png")?>" alt="mostrar-senha" id="pwd_img_register" onclick="togglePassword(this)" draggable="false">
                    </div>
                    <small><?=$errors['senha'] ?? ""?></small>
                </div>
                <div>
                    <div>
                        <input type="password" name="confirmacao_senha" class="input_password" id="input_password_register" placeholder="Confirmar senha">
                        <img src="<?=loadImage("assets/images/cadastro/show.png")?>" alt="mostrar-senha" id="pwd_img_register" onclick="togglePassword(this)" draggable="false">
                    </div>
                    <small><?=$errors['confirmacao_senha'] ?? ""?></small>
                    <small><?=$errors['senhas'] ?? ""?></small>
                </div>
                <input type="hidden" name="token" value="<?=$ses->get('token')?>">
            </section>
            <button type="submit">Cadastrar</button>
            <p>Já possui uma conta? <a href="<?=ROOT?>/login">Faça Login</a></p>
        </form>
    </main>
</body>
<script src="<?=ROOT?>/assets/js/cadastro.js"></script>
</html>