    <?=$this->view('header',['title'=>'Moradias','css'=>'moradias','pager'=>true])?>

    <style>
        :root{
            --favorito_img: url('<?=ROOT?>/assets/images/moradias/favoritoHover.png');
            --load_wifi_img: url('<?=ROOT?>/assets/images/moradias/AnimalsBlue.png');
            --load_dinner_img: url('<?=ROOT?>/assets/images/moradias/LeisureBlue.png');
            --load_leisure_img: url('<?=ROOT?>/assets/images/moradias/DinnerBlue.png');
            --load_parking_img: url('<?=ROOT?>/assets/images/moradias/WifiBlue.png');
            --load_animals_img: url('<?=ROOT?>/assets/images/moradias/ParkingBlue.png');
            --load_favorite_img: url('<?=ROOT?>/assets/images/moradias/favoritoRed.png');
        }
    </style>
    <section class="wrapper">
        <main>
            <section class="filtro">
                <h1 class="filtro_h1_1">Filtrar por:</h1>
                <h1 class="filtro_h1_2" onclick="toggleFiltroMenu()">- Filtros -</h1>
                <form method="post" class="filtro_form" id="filtragem">

                    <section>

                        <p class="title_endereco">- Endereço -</p>

                        <div class="filtro_endereco">

                            <label for="endereco_cep">CEP:</label>
                            <input type="text" name="cep" id="endereco_cep" oninput="formatCep(this.value);filter_overlay(this,'input');blockCep(this)" placeholder="00000-000" value="<?=$filter_settings['cep'] ?? ""?>">
                            <?php if(isset($errors['cep'])) : ?>
                                <small class="filtro_errors"><?=$errors['cep']?></small>
                            <?php endif ; ?>

                            <label for="endereco_cidade">Cidade:</label>
                            <select name="cidade" id="endereco_cidade" onchange="filter_overlay(this,'select')">
                                <option value="">-</option>
                                <?php foreach ($cities as $city) : ?>

                                    <?php if (isset($filter_settings['cidade']) && $filter_settings['cidade'] == $city) : ?>
                                        
                                        <option value="<?=$city?>" selected><?=esc($city)?></option>
                                    
                                    <?php continue; endif ; ?>
                                    
                                    <option value="<?=$city?>"><?=esc($city)?></option>
                                
                                <?php endforeach ;?>
                            </select>
                        
                            <label for="endereco_estado">Estado:</label>
                            <select name="uf" id="endereco_estado" onchange="filter_overlay(this,'select')">
                                <option value="">-</option>
                                <?php foreach ($states as $state) : ?>

                                    <?php if (isset($filter_settings['uf']) && $filter_settings['uf'] == $state) : ?>

                                        <option value="<?=$state?>" selected><?=esc($state)?></option>

                                    <?php continue; endif ; ?>
                                    
                                    <option value="<?=$state?>"><?=esc($state)?></option>
                                
                                <?php endforeach ;?>
                            </select>

                        </div>

                    </section>

                    <section>
                        <p class="title_categoria">- Categoria -</p>

                        <div class="filtro_categoria">
                            <?php foreach ($categories as $category) : ?>

                                <?php if (isset($filter_settings[$category]) && $filter_settings[$category]) : ?>

                                    <input type="checkbox" onclick="filter_overlay(this,'check')" name="<?=$category?>" value=1 id="<?=$category?>" checked>
                                    <label for="<?=$category?>"><?=ucwords(preg_replace("/_/"," ",$category))?></label>
                                    
                                <?php continue; endif ; ?>

                                <input type="checkbox" onclick="filter_overlay(this,'check')" name="<?=$category?>" value=1 id="<?=$category?>">
                                <label for="<?=$category?>"><?=ucwords(preg_replace("/_/"," ",$category))?></label>

                            <?php endforeach ;?>
                        </div>
                        
                    </section>

                    <section>
                        <p class="title_preco">- Preço -</p>

                        <div class="filtro_preco">
                            <label for="preco_minimo">Min:</label>
                            <input type="text" name="preco_min" id="preco_minimo" placeholder="R$0,00" oninput="formatCurrency(this,99999999);filter_overlay(this,'input')" class="precos" value="<?=$filter_settings['preco_min'] ?? ""?>">
                            <?php if(isset($errors['preco_min'])) : ?>
                                <small class="filtro_errors"><?=$errors['preco_min']?></small>
                            <?php endif ; ?>
                
                            <label for="preco_maximo">Max:</label>
                            <input type="text" name="preco_max" id="preco_maximo" placeholder="R$0,00" oninput="formatCurrency(this,99999999);filter_overlay(this,'input')" class="precos" value="<?=$filter_settings['preco_max'] ?? ""?>">
                            <?php if(isset($errors['preco_max'])) : ?>
                                <small class="filtro_errors"><?=$errors['preco_max']?></small>
                            <?php endif ; ?>

                        </div>

                    </section>

                    <section>

                        <p class="title_interior">- Interior -</p>

                        <div class="filtro_interior">

                            <label for="comodos">N° de cômodos:</label>
                            <input type="number" name="numero_comodos" id="comodos" placeholder="0" oninput="formatNum(this);filter_overlay(this,'input')" maxlength = "2" value="<?=$filter_settings['numero_comodos'] ?? ""?>"> 
                            <?php if(isset($errors['numero_comodos'])) : ?>
                                <small class="filtro_errors"><?=$errors['numero_comodos']?></small>
                            <?php endif ; ?>

                        </div>

                    </section>

                    <section>
                        <p class="title_extras">- Possui -</p>

                        <div class="filtro_extras">
                            <input type="checkbox" name="wifi" id="wifi" onclick="changeImg(this);filter_overlay(this,'check')" value=1 <?=$filter_settings['wifi'] ?? false ? "checked" : ""?>>
                            <label for="wifi"><img src="<?=ROOT?>/assets/images/moradias/Wifi<?=$filter_settings['wifi'] ?? false ? "Blue" : ""?>.png" alt="wifi-icon">Wi-Fi</label>
                            
                            <input type="checkbox" name="refeicao" id="refeicao" onclick="changeImg(this);filter_overlay(this,'check')" value=1 <?=$filter_settings['refeicao'] ?? false ? "checked" : ""?>>
                            <label for="refeicao"><img src="<?=ROOT?>/assets/images/moradias/Dinner<?=$filter_settings['refeicao'] ?? false ? "Blue" : ""?>.png" alt="refeicao-icon">Refeição</label>
                            
                            <input type="checkbox" name="lazer" id="lazer" onclick="changeImg(this);filter_overlay(this,'check')" value=1 <?=$filter_settings['lazer'] ?? false ? "checked" : ""?>>
                            <label for="lazer"><img src="<?=ROOT?>/assets/images/moradias/Leisure<?=$filter_settings['lazer'] ?? false ? "Blue" : ""?>.png" alt="lazer-icon">Área de Lazer</label>
                            
                            <input type="checkbox" name="estacionamento" id="estacionamento" onclick="changeImg(this);filter_overlay(this,'check')" value=1 <?=$filter_settings['estacionamento'] ?? false ? "checked" : ""?>>
                            <label for="estacionamento"><img src="<?=ROOT?>/assets/images/moradias/Parking<?=$filter_settings['estacionamento'] ?? false ? "Blue" : ""?>.png" alt="estacionamento-icon">Estacionamento</label>
                            
                            <input type="checkbox" name="animais" id="animais" onclick="changeImg(this);filter_overlay(this,'check')" value=1 <?=$filter_settings['animais'] ?? false ? "checked" : ""?>>
                            <label for="animais"><img src="<?=ROOT?>/assets/images/moradias/Animals<?=$filter_settings['animais'] ?? false ? "Blue" : ""?>.png" alt="animais-icon">Permite Animais</label>
                        </div>
                        
                        <input type="hidden" name="token" value="<?=$ses->get('token')?>">
                    </section>


                </form>

            </section>

            <section class="moradias-overlay">
                <div class="moradias-overlay-area">
                    <button type="submit" form="filtragem" formmethod="post">Aplicar Alterações</button>
                </div>
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
                                <p><img src="<?=loadImage("assets/images/publicacoes/TypeBlue.png","none")?>" alt="localizar-icon"><?=esc($row['tipo'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/LocationBlue.png","none")?>" alt="localizar-icon"><?=esc($row['cidade'])?>, <?=esc($row['uf'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/AddressBlue.png","none")?>" alt="endereco-icon"><?=esc($row['logradouro'])?> - <?=esc($row['bairro'])?>, <?=esc($row['numero'])?></p>
                                <p><img src="<?=loadImage("assets/images/publicacoes/RoomsBlue.png","none")?>" alt="comodos-icon"><?=esc($row['numero_comodos'])?> comodos</p>
                                <?php if (isset($row["telefone"]) && !empty($row["telefone"])) : ?>
                                    <p><img src="<?=loadImage("assets/images/publicacoes/PhoneBlue.png","none")?>" alt="telefone-icon"><?=esc(formatPhone($row["telefone"]))?></p>
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
                                <img src="<?=ROOT?>/assets/images/moradias/favorito<?=$favorites[$row['id_moradia']] ?? ''?>.png" class="img_favorito" onclick="favorite(this,<?=$row['id_moradia']?>)">
                            </div>
                        </section>

                    <?php endforeach ; ?>

                    <?=$pager->display()?>
                    <?php unset($rows); ?>
                    
                <?php else : ?>
                        
                    <div class="dhave">
                        <img src="<?=ROOT?>/assets/images/moradias/not-found.png">
                        <h1>Não foram encontradas moradias com essas configurações</h1>
                        <a href="<?=ROOT?>/moradias/reset">REINICIAR FILTROS</a>
                        <button type="submit" form="filtragem" formmethod="post">APLICAR ALTERAÇÕES</button>
                    </div>

                <?php endif ; ?>
                    
            </section>
        </main>
    </section>
</body>
<script>
    const favorite = function (element,id) {
        form = new FormData();

        form.append('id_moradia',id);
        form.append('token','<?=$ses->get("token")?>');

        ajax = new XMLHttpRequest;

        ajax.addEventListener('readystatechange',function (e) {
            if (ajax.readyState == 4 && ajax.status == 200) {
                handle_result(ajax.responseText,element);
            }
        });

        ajax.open('post','<?=ROOT?>/moradias/favorite',true);
        ajax.send(form);
    }

    const handle_result = function (result,element) {
        result = JSON.parse(result);
        if (result['success']) {
            if (result['mode'] == 'add') {
                element.src = "<?=ROOT?>/assets/images/moradias/favoritoRed.png";
            }
            if (result['mode'] == 'delete') {
                element.src = "<?=ROOT?>/assets/images/moradias/favorito.png";
            }
        } else {
            window.location.href = "<?=ROOT?>/login";
        }
    }
</script>
<script src="<?=ROOT?>/assets/js/moradias.js"></script>
</html>