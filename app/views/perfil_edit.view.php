    <?=$this->view('header',['title'=>'Editar Perfil','css'=>'perfil'])?>

    <section class="wrapper">

        <form method="post" class="user" enctype="multipart/form-data">
            <section class="user_pfp">

                <small style="color: var(--blueColor);">Clique para alterar a foto de perfil</small>

                <label class="edit_pfp_img_border" for="pfp">
                    <img class="user_pfp_img" src="<?=loadImage($ses->getUser('pfp'),'user')?>"></img>
                    <input type="file" name="pfp" id="pfp" accept="image/*" onchange="display_image(this.files[0])">
                    <img class="edit_pfp_img" src="<?=ROOT?>/assets/images/perfil/add-photo.png" alt="Alterar foto">
                </label>

                <small style="align-self: center;"><?=$errors['pfp'] ?? ""?></small>

                <p><?=esc($ses->getUser('nome_completo',$ses->getUser('nome_usuario')))?></p>

            </section>

            <script>
                let display_image = function (file) {
                    let limit = 32;
                    let allowed = ['png','jpg','jpeg','webp'];
                    let ext = file.name.split('.').pop();
                    if (!allowed.includes(ext)) {
                        alert("Formato não suportado");
                        return;
                    }
                    if (file.size > limit*1024*1024) {
                        alert("Limite de tamanho para este arquivo 3MB");
                        return;
                    }

                    document.querySelector(".user_pfp_img").src = URL.createObjectURL(file);
                }
            </script>

            <section class="user_info">

                <div class="user_dados">

                    <h2>DADOS DA CONTA</h2>

                    <p>Nome: <input type="text" name="nome_completo" value="<?=oldValue('nome_completo',$ses->getUser('nome_completo'))?>"></p>
                    <small><?=$errors['nome_completo'] ?? ""?></small>

                    <p>Nome de Usuário: <input type="text" name="nome_usuario" value="<?=oldValue('nome_usuario',$ses->getUser('nome_usuario'))?>"></p>
                    <small><?=$errors['nome_usuario'] ?? ""?></small>


                    <p>Email: <input type="email" name="email" value="<?=oldValue('email',$ses->getUser('email'))?>"></p>
                    <small><?=$errors['email'] ?? ""?></small>

                    <small style="align-self: center;"><?=$errors['update'] ?? ""?></small>

                </div>

                <div class="user_contato">

                    <h2>MEIOS DE CONTATO</h2>


                    <p>Email de Contato: <input type="text" name="email_contato" value="<?=oldValue('email_contato',$ses->getUser('email_contato'))?>"></p>
                    <small><?=$errors['email_contato'] ?? ""?></small>

                    <p>Telefone: <input type="text" name="telefone" id="input_phone" value="<?=oldValue('telefone',$ses->getUser('telefone'))?>"></p>
                    <small><?=$errors['telefone'] ?? ""?></small>
                    
                </div>
                
            </section>

            <section class="user-buttons">
                <div>
                    <a href="<?=ROOT?>/perfil/reset">Alterar Senha</a>
                    <button type="submit">Salvar Alterações</button>
                </div>
            </section>

            <input type="hidden" name="token" value="<?=$ses->get('token')?>">
            
        </form>
        
    </section>

</body>
<script src="<?=ROOT?>/assets/js/perfil.js"></script>
</html>