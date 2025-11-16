    <?=$this->view('header',['title'=>'Seu Perfil','css'=>'perfil'])?>

    <section class="wrapper">

        <main class="user">

            <section class="user_pfp">

                <?php if (message()) : ?>
                    <small class="message" style="color: var(--blueColor);"><?=message('',true)?></small>
                <?php endif ; ?>
                
                <div class="user_pfp_img_border">
                    <img class="user_pfp_img" src="<?=loadImage($ses->getUser('pfp'),'user')?>"></img>
                </div>

                <p><?=esc($ses->getUser('nome_completo',$ses->getUser('nome_usuario')))?></p>

            </section>
            <section class="user_info">

                <div class="user_dados">

                    <h2>DADOS DA CONTA</h2>
                    
                    <p>Nome: <span><?=esc($ses->getUser('nome_completo','Indefinido'))?></span></p>
                    <p>Nome de Usuário: <span><?=esc($ses->getUser('nome_usuario'))?></span></p>
                    <p>Email: <span><?=esc($ses->getUser('email'))?></span></p>

                    <script>

                        const delete_account = function(){
                            if (confirm("Você tem certeza?")) {
                                const form = new FormData();

                                form.append('token','<?=$ses->get('token')?>');
                                
                                const ajax = new XMLHttpRequest;

                                ajax.addEventListener('readystatechange',function(e){
                                    if (ajax.readyState == 4 && ajax.status == 200) {
                                        handle_result(ajax.responseText);
                                    }
                                });

                                ajax.open('post','<?=ROOT?>/perfil/delete',true);
                                ajax.send(form);
                            }
                        }

                        const handle_result = function(result){
                            if (alert) {
                                window.location.href = "<?=ROOT?>/cadastro";
                            } else {
                                alert("Algo deu errado tente novamente mais tarde");
                                location.reload();
                            }
                        }

                    </script>

                </div>

                <div class="user_contato">
                    <h2>MEIOS DE CONTATO</h2>
                    <p>Email de Contato: <span><?=esc($ses->getUser('email_contato','Indefinido'))?></span></p>
                    <p>Telefone: <span><?=esc($ses->getUser('telefone','Indefinido'))?></span></p>
                </div>

            </section>
            
            <section class="user-buttons">
                <div>
                    <a href="<?=ROOT?>/perfil/edit">Editar Conta</a>
                    <button onclick="delete_account()">Deletar Conta</button>
                </div>
            </section>

        </main>

    </section>

</body>
</html>