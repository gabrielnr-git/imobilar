    <?=$this->view('header',['title'=>$row['nome_usuario'] ?? '404','css'=>'perfil'])?>

    <section class="wrapper">

        <main class="user">
            <?php if ($row) :?>
                <section class="user_pfp">

                    <div class="user_pfp_img_border">
                        <img class="user_pfp_img" src="<?=loadImage($row['pfp'],'user')?>"></img>
                    </div>

                    <p><?=esc($row['nome_usuario'])?></p>

                </section>
                <section class="user_info">

                    <div class="user_dados">

                        <h2>DADOS DA CONTA</h2>
                        
                        <p>Nome: <span><?=esc($row['nome_completo'] ?? 'indefinido')?></span></p>
                        <p>Nome de Usuário: <span><?=esc($row['nome_usuario'])?></span></p>
                        <p>Email: <span><?=esc($row['email'])?></span></p>

                    </div>

                    <div class="user_contato">
                        <h2>MEIOS DE CONTATO</h2>
                        <p>Email de Contato: <span><?=esc($row['email_contato'] ?? 'indefinido')?></span></p>
                        <p>Telefone: <span><?=esc($row['telefone'] ?? 'indefinido')?></span></p>
                    </div>

                </section>

                <?php if ($ses->is_admin() && $row['cargo'] != 'administrador') : ?>
                    <section class="user-buttons">
                        <button onclick="delete_account()">Deletar Conta</button>
            
                    </section>

                    <script>
                        const delete_account = function() {
                            if (confirm("Deletar esta conta?")) {
                                const form = new FormData();

                                form.append('id_usuario',<?=URL('id')?>);

                                const ajax = new XMLHttpRequest;

                                ajax.addEventListener('readystatechange',function(e){
                                    if (ajax.readyState == 4 && ajax.status == 200) {
                                        handle_result(ajax.responseText);
                                    }
                                });

                                ajax.open('post','<?=ROOT?>/admin/deleteUser',true);
                                ajax.send(form);
                            }
                        }

                        const handle_result = function (result) {
                            if (result) {
                                location.reload();
                            } else {
                                alert("Algo deu errado!");
                            }
                        }
                    </script>
                <?php endif ; ?>

            <?php else : ?>
                <section class="user-dhave">
                    <img src="<?=loadImage("assets/images/perfil/not-found.png","none")?>" alt="">
                    <h2>USUÁRIO NÃO ENCONTRADO</h2>
                    <a href="<?=ROOT?>/perfil">Retornar</a>
                </section>
            <?php endif ; ?>

        </main>

    </section>
</body>
</html>