    <?=$this->view('header',['title'=>'Recuperar Senha','css'=>'login'])?>

    <section class="wrapper">

        <main class="recuperar">

            <?php if (isset($stage) && $stage == "recuperar") : ?> 

                <form method="post" class="recuperar-form">

                    <h2>Esqueceu a senha?</h2>

                    <label for="email">
                        <img src="<?=loadImage("assets/images/login/Email.png","none")?>" alt="" draggable="false">
                        <input type="email" name="email" id="email" placeholder="Seu email cadastrado">
                    </label>
                    <small><?=$errors['email'] ?? ""?></small>

                    <input type="hidden" name="token" value="<?=$ses->get('token')?>">

                    <button type="submit" onclick="showLoading()">Continuar</button>

                </form>

            <?php elseif (isset($stage) && $stage == "codigo") : ?> 

                <form method="post" class="recuperar-form">

                    <p>Digite o código que foi enviado para o e-mail: <span><?=$row['email']?></span></p>

                    <label for="code">
                        <img src="<?=loadImage("assets/images/login/Password.png","none")?>" alt="" draggable="false">
                        <input type="number" name="code" id="code" placeholder="Codigo" oninput="limit(this,6);numberOnly(this)">
                    </label>
                    <small><?=$errors['code'] ?? ""?></small>

                    <?php if ($interval < 60 && $interval > 0) : ?>
                        <div>
                            <a class="disabled">Reenviar Código (<?=$interval?> segundos restantes)</a>
                        </div>
                    <?php else : ?>
                        <div>
                            <a class="enabled" onclick="resend()">Reenviar Código</a>
                        </div>
                    <?php endif ; ?>
                    <small><?=$errors['interval'] ?? ""?></small>
                        
                    <input type="hidden" name="token" value="<?=$ses->get('token')?>">

                    <button type="submit" onclick="showLoading()">Continuar</button>

                </form>

                <?php if ($interval >= 60 || $interval <= 0) : ?>

                    <script>
                        const resend = function() {
                            const form = new FormData();
                            form.append('link','<?=URL("id")?>');
                            form.append('login','codigo');
                            form.append('type','ForgotPwd');
                            form.append('Subject','ForgotPwd');

                            const ajax = new XMLHttpRequest;

                            ajax.addEventListener("readystatechange",function(e){
                                if (ajax.readyState == 4 && ajax.status == 200) {
                                    handle_result(ajax.responseText);
                                }
                            });

                            ajax.open('post','<?=ROOT?>/login/resend',true)
                            ajax.send(form);
                            showLoading();
                        }

                        const handle_result = function (result) {
                            result = JSON.parse(result);
                            if (result.success) {
                                const redirect = "<?=ROOT?>/"+result.redirect;
                                window.location.href = redirect;
                            } else {
                                alert("Algo deu errado!");
                                location.reload();
                            }
                        }
                    </script>

                <?php endif ; ?>

            <?php elseif (isset($stage) && $stage == "alterar") : ?> 

                <form method="post" class="recuperar-form">

                    <h2>Alterar senha</h2>

                    <label for="senha">
                        <img src="<?=loadImage("assets/images/login/Password.png","none")?>" alt="" draggable="false">
                        <input type="password" name="senha" id="senha" placeholder="Digite sua nova senha">
                        <img src="<?=loadImage('assets/images/login/show.png')?>" alt="mostrar-senha" onclick="togglePassword(this)">
                    </label>
                    <small><?=$errors['senha'] ?? ""?></small>

                    <label for="senha_confirm">
                        <img src="<?=loadImage("assets/images/login/Password.png","none")?>" alt="" draggable="false">
                        <input type="password" name="confirmacao_senha" id="senha_confirm" placeholder="Digite sua nova senha novamente">
                        <img src="<?=loadImage('assets/images/login/show.png')?>" alt="mostrar-senha" onclick="togglePassword(this)">
                    </label>
                    <small><?=$errors['confirmacao_senha'] ?? ""?></small>
                    <small><?=$errors['senhas'] ?? ""?></small>

                    <input type="hidden" name="token" value="<?=$ses->get('token')?>">

                    <button type="submit" onclick="showLoading()">Continuar</button>

                </form>

            <?php endif ; ?> 

        </main>

        <section class="overlay">
            <div class="loading">
                <img src="<?=loadImage("assets/images/publicar/loading.gif","none")?>" alt="loading-gif" draggable="false">
            </div>
        </section>
        
    </section>
    
</body>
<script src="<?=ROOT?>/assets/js/login.js"></script>
</html>