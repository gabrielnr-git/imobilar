    <?=$this->view('header',['title'=>'Verificar Email','css'=>'login'])?>

    <section class="wrapper">

        <main class="recuperar">

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
                        form.append('get','active');
                        form.append('login','ativar');
                        form.append('type','2FA');
                        form.append('subject','Verificar Email');

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