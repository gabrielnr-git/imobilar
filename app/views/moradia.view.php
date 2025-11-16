    <?=$this->view('header',['title'=>'Moradia','css'=>'moradia'])?>

    <section class="wrapper">

        <main class="moradia">

            <?php if ($row) : ?>
    
                <section class="detalhes1">
    
                    <div class="detalhes1-images">
                        <div class="detalhes1-images-buttons" onclick="change_image('prev')" draggable="false">
                            <img src="<?=loadImage("assets/images/moradia/prev.png","none")?>">
                        </div>
                        
                        <img src="<?=loadImage($row['imagem1'])?>" class="detalhes1-images-items">
                        
                        <?php if (!empty($row['imagem2'])) : ?>
                            <img src="<?=loadImage($row['imagem2'])?>" class="detalhes1-images-items">
                        <?php endif; ?>
                        
                        <?php if (!empty($row['imagem3'])) : ?>
                            <img src="<?=loadImage($row['imagem3'])?>" class="detalhes1-images-items">
                        <?php endif; ?>
                        
                        <?php if (!empty($row['imagem4'])) : ?>
                            <img src="<?=loadImage($row['imagem4'])?>" class="detalhes1-images-items">
                        <?php endif; ?>
                        
                        <?php if (!empty($row['imagem5'])) : ?>
                            <img src="<?=loadImage($row['imagem5'])?>" class="detalhes1-images-items">
                        <?php endif; ?>
                        
                        <div class="detalhes1-images-buttons" onclick="change_image('next')">
                            <img src="<?=ROOT?>/assets/images/moradia/next.png" draggable="false">
                        </div>
                    </div>
                    
                    <div class="detalhes1-vendedor">
    
                        <div class="detalhes1-vendedor-title">
                            <h1><?=esc($row['nome'])?></h1>
                            <?php if ($row['id_usuario'] == $ses->getUser('id_usuario') || $ses->is_admin()) : ?>
                                <p><?=esc($row['situacao'])?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="detalhes1-vendedor-contato">
    
                            <p><a href="<?=ROOT?>/perfil/user/<?=$row['id_usuario']?>"><img src="<?=loadImage("assets/images/moradia/UserBlue.png","none")?>"><?=esc($row['nome_completo'] ?? $row['nome_usuario'])?></a></p>
                            
                            <?php if (!empty($row['telefone'])) : ?>
                                <p><img src="<?=loadImage("assets/images/moradia/PhoneBlue.png","none")?>"><?=esc(formatPhone($row['telefone']))?></p>
                            <?php endif ; ?>
                            
                            <p><img src="<?=loadImage("assets/images/moradia/EmailBlue.png","none")?>"><?=esc($row['email_contato'] ?? $row['email'])?></p>
                        
                        </div>
    
                        <div class="detalhes1-vendedor-comentario">
    
                            <p><img src="<?=loadImage("assets/images/moradia/CommentBlue.png","none")?>">Descrição:</p>
    
                            <span><?=esc($row['descricao'])?></span>
                        </div>
    
                    </div>
    
                </section>
    
                <section class="detalhes2">
    
                    <p class="detalhes2-preco">R$ <?=esc(formatPrice($row['preco']))?></p>
                    <div class="detalhes2-info">
    
                        <p><img src="<?=loadImage("assets/images/moradia/TypeBlue.png","none")?>">Tipo:</p>
                        <span><?=esc($row['tipo'])?></span>
    
                        <p><img src="<?=loadImage("assets/images/moradia/LocationBlue.png","none")?>">Localização:</p>
                        <span><?=esc($row['cidade'])?>, <?=esc($row['uf'])?> (<?=esc(formatCEP($row['cep']))?>)</span>
    
                        <p><img src="<?=loadImage("assets/images/moradia/AddressBlue.png","none")?>">Endereço:</p>
                        <span><?=esc($row['logradouro'])?> - <?=esc($row['bairro'])?>, <?=esc($row['numero'])?></span>
                        
                        <p><img src="<?=loadImage("assets/images/moradia/SizeBlue.png","none")?>">Tamanho:</p>
                        <span><?=esc($row['largura'])?>m x <?=esc($row['comprimento'])?>m</span>
                        
                        <p><img src="<?=loadImage("assets/images/moradia/RoomsBlue.png","none")?>">Total de Comodos:</p>
                        <span><?=esc($row['numero_comodos'])?></span>
    
                        <div class="extras">
                            <p <?=$row['wifi'] ? "class='active'": '' ;?> ><img src="<?=ROOT?>/assets/images/moradia/<?=$row['wifi'] ? 'WifiBlue' : 'Wifi';?>.png">Wi-fi</p>
                            <p <?=$row['refeicao'] ? "class='active'": '' ;?> ><img src="<?=ROOT?>/assets/images/moradia/<?=$row['refeicao'] ? 'DinnerBlue' : 'Dinner';?>.png">Refeição</p>
                            <p <?=$row['lazer'] ? "class='active'": '' ;?> ><img src="<?=ROOT?>/assets/images/moradia/<?=$row['lazer'] ? 'LeisureBlue' : 'Leisure';?>.png">Área de Lazer</p>
                            <p <?=$row['estacionamento'] ? "class='active'": '' ;?> ><img src="<?=ROOT?>/assets/images/moradia/<?=$row['estacionamento'] ? 'ParkingBlue' : 'Parking';?>.png">Estacionamento</p>
                            <p <?=$row['animais'] ? "class='active'": '' ;?> ><img src="<?=ROOT?>/assets/images/moradia/<?=$row['animais'] ? 'AnimalsBlue' : 'Animals';?>.png">Permite Animais</p>
                        </div>
    
                    </div>
    
                    <?php if ($row['id_usuario'] == $ses->getUser('id_usuario') || $ses->is_admin()) : ?>
                        <div class="detalhes2-control">
                            <?php if ($ses->is_admin()) : ?>
                                <?php if ($row['situacao'] == "Em Análise") : ?>
                                    <button onclick="approve(<?=$row['id_moradia']?>,'accept')">APROVAR</button>
                                    <button onclick="approve(<?=$row['id_moradia']?>,'reject')">REJEITAR</button>
                                <?php else : ?>
                                    <button onclick="approve(<?=$row['id_moradia']?>,'suspend')">SUSPENDER</button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="<?=ROOT?>/moradia/edit/<?=$row['id_moradia']?>">EDITAR</a>
                            <button onclick="delete_moradia()">DELETAR</button>
                        </div>
    
                        <script>
                            const delete_moradia = function () {
                                if (confirm("Você tem certeza?")) {
                                    const form = new FormData();
                                    form.append('id',<?=URL('id')?>);
                                    form.append('token','<?=$ses->get('token')?>');
    
                                    const ajax = new XMLHttpRequest;
    
                                    ajax.addEventListener('readystatechange',function (e) {
                                        if (ajax.readyState == 4 && ajax.status == 200) {
                                            handle_result(ajax.responseText);
                                        }
                                    })
    
                                    ajax.open('post','<?=ROOT?>/moradia/delete',true);
                                    ajax.send(form);
                                }
                            }
    
                            const handle_result = function(result){
                                result = JSON.parse(result);
                                if (result.success) {
                                    window.location.href = "<?=ROOT?>/perfil";
                                } else {
                                    alert(result.message);
                                    location.reload();
                                }
                            }
    
                            <?php if ($ses->is_admin()) : ?>
                                const approve = function(id,action,element) {
                                    let message = "";
                                    if (action == 'accept') {message = "Aprovar moradia de id: "+id+"?";}
                                    else if (action == 'reject') {message = "Reprovar moradia de id: "+id+"?";}
                                    else if (action == 'suspend') {message = "Suspender moradia de id: "+id+"?";}
    
                                    if (confirm(message)) {
                                        const form = new FormData();
    
                                        form.append('id_moradia',id);
                                        form.append('action',action);
    
                                        const ajax = new XMLHttpRequest;
    
                                        ajax.addEventListener('readystatechange',function (e) {
                                            if (ajax.readyState == 4 && ajax.status == 200) {
                                                if (ajax.responseText) {
                                                    location.reload();
                                                } else {
                                                    alert("Algo deu errado");
                                                }
                                            }
                                        });
    
                                        ajax.open('post','<?=ROOT?>/admin/approve',true);
                                        ajax.send(form);
                                    }
                                }
                            <?php endif; ?>
    
                        </script>
                    <?php endif ; ?>
    
                </section>

            <?php else : ?>
                <section class="moradia-dhave">
                    <img src="<?=loadImage("assets/images/moradia/not-found.png","none")?>" alt="">
                    <h2>MORADIA NÃO ENCONTRADA</h2>
                    <a href="<?=ROOT?>/moradias">RETORNAR</a>
                </section>
            <?php endif ; ?>

        </main>

    </section>
</body>
<script src="<?=ROOT?>/assets/js/moradia.js"></script>
</html>
<?php unset($row); ?>