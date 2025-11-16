    <?=$this->view('header',['title'=>'Favoritos','css'=>'favoritos'])?>

    <style>
        :root{
            --favorite_hover_img: url('<?=ROOT?>/assets/images/favoritos/favoritoHover.png');
            --favoriteRed_img: url('<?=ROOT?>/assets/images/favoritos/favoritoRed.png');
        }
    </style>
    <section class="wrapper">

        <main class="favoritos">
            
             <?php if ($rows) : ?>

                <section class="moradias">

                    <?php foreach($rows as $row) : ?>

                        <section class="item_lista">
                            <div class="casa_img">
                                <img src="<?=loadImage($row['imagem1'])?>" alt="house-img">
                            </div>
                            <div class="casa_info">
                                <h2><?=esc($row['nome'])?></h2>
                                <p><img src="<?=loadImage("assets/images/publicacoes/TypeBlue.png","none")?>" alt="localizar-icon"><?=esc($row['tipo'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/LocationBlue.png","none")?>" alt="localizar-icon"><?=esc($row['cidade'])?>, <?=esc($row['uf'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/AddressBlue.png","none")?>" alt="endereco-icon"><?=esc($row['logradouro'])?> - <?=esc($row['bairro'])?>, <?=esc($row['numero'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/RoomsBlue.png","none")?>" alt="comodos-icon"><?=esc($row['numero_comodos'])?> comodos</p>
                                <?php if (isset($row["telefone"]) && !empty($row["telefone"])) : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/PhoneBlue.png","none")?>" alt="telefone-icon"><?=esc(formatPhone($row['telefone']))?></p>
                                <?php elseif (isset($row["email_contato"]) && !empty($row["email_contato"])) : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/EmailBlue.png","none")?>" alt="email-icon"><?=esc($row["email_contato"])?></p>
                                <?php else  : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/EmailBlue.png","none")?>" alt="email-icon"><?=esc($row["email"])?></p>
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
                                <img src="<?=loadImage("assets/images/moradias/favoritoRed.png","none")?>" alt="favoritar" class="img_favorito" onclick="favorite(this,<?=$row['id_moradia']?>)">
                            </div>
                        </section>

                    <?php endforeach ; ?>

                    <?=$pager->display()?>

                    <?php unset($rows); ?>
                    
                </section>
            <?php else : ?>
                <section class="favoritos-dhave">
                    <img src="<?=loadImage("assets/images/favoritos/bookmark.png",'none')?>" alt="">
                    <h2>Você não possui moradias favoritadas no momento</h2>
                    <a href="<?=ROOT?>/moradias">PROCURAR MORADIAS</a>
                </section>
            <?php endif ; ?>

        </main>

    </section>
</body>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    let count = document.querySelector(".moradias").childElementCount - 1;

    const favorite = function (element,id) {
        form = new FormData();

        form.append('id_moradia',id);

        ajax = new XMLHttpRequest;

        ajax.addEventListener('readystatechange',function (e) {
            if (ajax.readyState == 4 && ajax.status == 200) {
                handle_result(ajax.responseText,element);
            }
        });

        ajax.open('post','<?=ROOT?>/favoritos/favorite',true);
        ajax.send(form);
    }

    const handle_result = function (result,element) {
        result = JSON.parse(result);
        if (result['success']) {
            element.parentNode.parentNode.remove();
            if (count == 1) {
                document.querySelector(".pager-nav").remove();
                location.reload();
            }
            count--;
            console.log(count);
        } else {
            window.location.href = "<?=ROOT?>/login";
        }
    }
</script>
<script src="<?=ROOT?>/assets/js/favoritos.js"></script>
</html>