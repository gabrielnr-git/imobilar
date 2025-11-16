    <?=$this->view('header',['title'=>'Admin','css'=>'admin'])?>

    <section class="wrapper">
        
        <main class="admin">
        
            <section class="admin-general">
                <div class="general-item">
                    <div>
                        <h2>Número total de moradias: </h2>
                        <p><?=esc($general['total_moradias'])?></p>
                    </div>
                    <div>
                        <h2>Número total de moradias aprovadas: </h2>
                        <p><?=esc($general['total_aprovadas'])?></p>
                    </div>
                    <div>
                        <h2>Número total de moradias rejeitadas: </h2>
                        <p><?=esc($general['total_rejeitadas'])?></p>
                    </div>
                    <div>
                        <h2>Número total de moradias em análise: </h2>
                        <p><?=esc($general['total_analise'])?></p>
                    </div>
                    <div>
                        <h2>Número total de moradias nos ultimos 30 dias: </h2>
                        <p><?=esc($general['total_30dias'])?> (~<?=esc(ceil($general['total_30dias'] / 30))?>/dia)</p>
                    </div>
                    <div>
                        <h2>Número total de moradias hoje: </h2>
                        <p><?=esc($general['total_hoje'])?></p>
                    </div>
                </div>

                <div class="general-item">
                    <div>
                        <h2>Número total de usuários:</h2>
                        <p><?=esc($general['total_usuarios'])?></p>
                    </div>

                    <div>
                        <h2>Usuários recentes:</h2>
                        <?php foreach ($general['recents'] as $recent) : ?>
                            <p>
                                <a href="<?=ROOT?>/perfil/user/<?=esc($recent['id_usuario'])?>"><?=esc($recent['nome_usuario'])?></a>
                                <span><?=esc(formatDate($recent['criacao']))?></span>
                            </p>
                        <?php endforeach ?>
                    </div>
                </div>

                <?php if (url('id') === 'reject') : ?>
                    <a class="general-button" href="<?=ROOT?>/admin/dashboard">Ver moradias a serem aprovadas</a>
                <?php else : ?>
                    <a class="general-button" href="<?=ROOT?>/admin/dashboard/reject">Ver moradias recusadas</a>
                <?php endif ; ?>

            </section>

            <section class="moradias">

                <?php if ($rows) : ?>

                    <?php foreach($rows as $row) : ?>

                        <section class="item_lista">
                            <div class="casa_img">
                                <img src="<?=loadImage($row['imagem1'])?>" alt="house-img">
                            </div>
                            <div class="casa_info">
                                <h2><?=esc($row['nome'])?></h2>
                                <p><img src="<?=ROOT?>/assets/images/publicacoes/TypeBlue.png" alt="localizar-icon"><?=esc($row['tipo'])?></p>
                                <p><img src="<?=ROOT?>/assets/images/publicacoes/LocationBlue.png" alt="localizar-icon"><?=esc($row['cidade'])?>, <?=esc($row['uf'])?></p>
                                <p><img src="<?=ROOT?>/assets/images/publicacoes/AddressBlue.png" alt="endereco-icon"><?=esc($row['logradouro'])?> - <?=esc($row['bairro'])?>, <?=esc($row['numero'])?></p>
                                <p><img src="<?=ROOT?>/assets/images/publicacoes/RoomsBlue.png" alt="comodos-icon"><?=esc($row['numero_comodos'])?> comodos</p>
                                <?php if (isset($row['telefone']) && !empty($row['telefone'])) : ?>
                                    <p><img src="<?=ROOT?>/assets/images/publicacoes/PhoneBlue.png" alt="telefone-icon"><?=esc(formatPhone($row['telefone']))?></p>
                                <?php elseif (isset($row['email_contato']) && !empty($row['email_contato'])) : ?>
                                    <p><img src="<?=ROOT?>/assets/images/publicacoes/EmailBlue.png" alt="telefone-icon"><?=esc($row['email_contato'])?></p>
                                <?php else  : ?>
                                    <p><img src="<?=ROOT?>/assets/images/publicacoes/EmailBlue.png" alt="telefone-icon"><?=esc($row['email'])?></p>
                                <?php endif ; ?>
                                <div class="casa_detalhes">
                                    <div class="casa_detalhes_extras">
                                        <img src="<?=ROOT?>/assets/images/publicacoes/Wifi<?php if ($row['wifi']) echo "Blue"; ?>.png" alt="wifi-icon">
                                        <img src="<?=ROOT?>/assets/images/publicacoes/Dinner<?php if ($row['refeicao']) echo "Blue"; ?>.png" alt="refeicao-icon">
                                        <img src="<?=ROOT?>/assets/images/publicacoes/Leisure<?php if ($row['lazer']) echo "Blue"; ?>.png" alt="lazer-icon">
                                        <img src="<?=ROOT?>/assets/images/publicacoes/Parking<?php if ($row['estacionamento']) echo "Blue"; ?>.png" alt="estacionamento-icon">
                                        <img src="<?=ROOT?>/assets/images/publicacoes/Animals<?php if ($row['animais']) echo "Blue"; ?>.png" alt="animais-icon">
                                    </div>
                                    <div class="casa_detalhes_preco">
                                        <p>R$ <?=esc(formatPrice($row['preco']))?></p>
                                        <a href="<?=ROOT."/moradia/id/".$row['id_moradia']?>">Mais Detalhes</a>
                                    </div>
                                </div>
                                <div class="casa_adm">
                                    <button onclick="approve(<?=$row['id_moradia']?>,'accept',this)">Aprovar</button>
                                    <?php if ($situacao == "Em Análise") : ?>
                                            <button onclick="approve(<?=$row['id_moradia']?>,'reject',this)">Recusar</button>
                                    <?php elseif ($situacao == "Rejeitado") : ?>
                                            <button onclick="approve(<?=$row['id_moradia']?>,'suspend',this)">Suspender</button>
                                    <?php endif ;?>
                                    <button onclick="approve(<?=$row['id_moradia']?>,'remove',this)">Remover</button>
                                    <span>ID <?=esc($row['id_moradia'])?></span>
                                </div>
                            </div>
                        </section>

                    <?php endforeach ; ?>
                    <?=$pager->display()?>
                    
                    <script>
                        const approve = function(id,action,element) {

                            let message = "";
                            if (action == 'accept') {message = "Aprovar moradia de id: "+id+"?";}
                            else if (action == 'reject') {message = "Reprovar moradia de id: "+id+"?";}
                            else if (action == 'suspend') {message = "Suspender moradia de id: "+id+"?";}
                            else if (action == 'remove') {message = "Deletar moradia de id: "+id+"?";}

                            if (confirm(message)) {
                                const form = new FormData();
    
                                form.append('id_moradia',id);
                                form.append('action',action);
    
                                const ajax = new XMLHttpRequest;
    
                                ajax.addEventListener('readystatechange',function (e) {
                                    if (ajax.readyState == 4 && ajax.status == 200) {
                                        if (ajax.responseText) {
                                            element.parentNode.parentNode.parentNode.remove();
                                        } else {
                                            alert("Algo deu errado!");
                                        }
                                        const count = document.querySelectorAll('.item_lista').length;
                                        if (count == 0) {
                                            location.reload();
                                        }
                                    }
                                });
    
                                ajax.open('post','<?=ROOT?>/admin/approve',true);
                                ajax.send(form);
                            }
                        }
                    </script>

                <?php else : ?>

                    <div class="dhave">
                        <img src="<?=ROOT?>/assets/images/publicacoes/dhave.png">
                        <?php if ($situacao == "Em Análise") : ?>
                                <h1>Não há publicacões a serem analisadas no momento.</h1>
                        <?php elseif ($situacao == "Rejeitado") : ?>
                                <h1>Não há publicacões (recusadas) a serem analisadas no momento.</h1>
                        <?php endif ;?>
                    </div>

                <?php endif ; ?>

            </section>

        </main>

    </section>
</body>
<script src="<?=ROOT?>/assets/js/admin.js"></script>
</html>