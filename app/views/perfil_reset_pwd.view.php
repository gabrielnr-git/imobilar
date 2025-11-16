    <?=$this->view('header',['title'=>'Alterar Senha','css'=>'perfil'])?>

    <section class="wrapper">

        <form method="post" class="user" id="reset" enctype="multipart/form-data">
            <section class="reset_data">
                <h2>Alterar Senha</h2>

                <?php if ($state) : ?>     
                    <label for="pwd">Digite sua nova senha:</label>

                    <div class="input_pwd">
                        <input type="password" name="senha" id="pwd">
                        <img onclick="togglePassword(this)" src="<?=loadImage("assets/images/perfil/show.png","none")?>" alt="mostrar-senha">
                    </div>

                    <small><?=$errors['senha'] ?? ""?></small>

                    <label for="pwd_again">Digite sua nova senha novamente:</label>

                    <div class="input_pwd">
                        <input type="password" name="confirmacao_senha" id="pwd_again">
                        <img onclick="togglePassword(this)" src="<?=loadImage("assets/images/perfil/show.png","none")?>" alt="mostrar-senha">
                    </div>

                    <small><?=$errors['confirmacao_senha'] ?? ""?></small>
                    <small><?=$errors['form'] ?? ""?></small>
                    
                    <input type="hidden" name="token" value="<?=$ses->get('token')?>">

                    <button type="submit">Alterar</button>
                <?php else : ?>
                    <label for="pwd">Digite sua senha atual:</label>
                    <div class="input_pwd">
                        <input type="password" name="senha" id="pwd">
                        <img onclick="togglePassword(this)" src="<?=loadImage("assets/images/perfil/show.png","none")?>" alt="mostrar-senha">
                    </div>

                    <small><?=$errors['senha'] ?? ""?></small>
                    
                    <input type="hidden" name="token" value="<?=$ses->get('token')?>">

                    <button type="submit">Próximo</button>
                <?php endif; ?>
                
            </section>
        </form>
        
    </section>

</body>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    
    const togglePassword = function (element) {
    const input = element.previousSibling.previousSibling;
    if (input.type == "password") {
        input.type = "text";
        element.src = element.src.slice(0,-8) + "hide.png";
    } else if (input.type == "text") {
        input.type = "password";
        element.src = element.src.slice(0,-8) + "show.png";
    }
}
</script>
</html>