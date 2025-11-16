    <?=$this->view('header',['title'=>'Suas Publicações','css'=>'publicacoes'])?>

    <section class="wrapper">

        <main class="publicacoes">
            
            <?php if ($rows) : ?>

                <section class="moradias">
                    <?php foreach($rows as $row) : ?>
                        <section class="item_lista">
                            <div class="casa_img">
                                <img src="<?=loadImage($row['imagem1'])?>" alt="house-img">
                            </div>
                            <div class="casa_info">
                                <span class="casa_status"><?=esc($row['situacao'])?></span>
                                <h2><?=esc($row['nome'])?></h2>
                                <p><img src="<?=loadImage("assets/images/publicacoes/TypeBlue.png","none")?>" alt="localizar-icon"><?=esc($row['tipo'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/LocationBlue.png","none")?>" alt="localizar-icon"><?=esc($row['cidade'])?>, <?=esc($row['uf'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/AddressBlue.png","none")?>" alt="endereco-icon"><?=esc($row['logradouro'])?> - <?=esc($row['bairro'])?>, <?=esc($row['numero'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/RoomsBlue.png","none")?>" alt="comodos-icon"><?=esc($row['numero_comodos'])?> comodos</p>
                                <?php if ($ses->getUser("telefone") !== '') : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/PhoneBlue.png","none")?>" alt="telefone-icon"><?=esc($ses->getUser("telefone"))?></p>
                                <?php elseif ($ses->getUser("email_contato") !== '') : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/EmailBlue.png","none")?>" alt="email-icon"><?=esc($ses->getUser("email_contato"))?></p>
                                <?php else  : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/EmailBlue.png","none")?>" alt="email-icon"><?=esc($ses->getUser("email"))?></p>
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
                                        <a href="<?=ROOT."/moradia/id/".$row['id_moradia']?>">Visualizar</a>
                                        <a href="<?=ROOT."/moradia/edit/".$row['id_moradia']?>">Editar</a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endforeach ; ?>
                    <?=$pager->display()?>

                </section>

            <?php else : ?>
                <section class="publicacoes-dhave">
                    <img src="<?=loadImage("assets/images/publicacoes/dhave.png","none")?>" alt="">
                    <h2>Você não possui publicações no momento</h2>
                    <a href="<?=ROOT?>/publicar">PUBLIQUE AGORA</a>
                </section>
            <?php endif ; ?>

        </main>

    </section>
</body>
<script src="<?=ROOT?>/assets/js/publicacoes.js"></script>
</html>