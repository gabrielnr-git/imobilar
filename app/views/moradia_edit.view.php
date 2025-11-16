    <?=$this->view('header',['title'=>'Editar Moradia','css'=>'publicar'])?>

    <style>
        :root{
            --load_wifi_img: url('<?=ROOT?>/assets/images/moradias/AnimalsBlue.png');
            --load_dinner_img: url('<?=ROOT?>/assets/images/moradias/LeisureBlue.png');
            --load_leisure_img: url('<?=ROOT?>/assets/images/moradias/DinnerBlue.png');
            --load_parking_img: url('<?=ROOT?>/assets/images/moradias/WifiBlue.png');
            --load_animals_img: url('<?=ROOT?>/assets/images/moradias/ParkingBlue.png');
        }
    </style>
    <main class="wrapper">
        <section class="criacao">
            <form class="criacao-form" id="criacao-form" method="post" enctype="multipart/form-data" onsubmit="submit_form(event)">

                <section class="criacao-upload">
                    <?php //Showing the database images  ?>
                    <?php $i=1; foreach ($row['images'] as $image) : ?>   
                        <div class="criacao-upload-image">
                            <img src="<?=ROOT.'/'.$image?>">
                            <img src="<?=loadImage("assets/images/publicar/remove.png","none")?>" id="delete-img" alt="delete" onclick="delete_image(this)" deleteID="<?="img_num".$i?>">
                        </div>
                    <?php $i++; endforeach ; ?>

                    <label class="criacao-upload-button">
                        <div for="input_file">
                            <input type="file" id="input_file" accept="image/*" onchange="display_images(this.files)" multiple>
                            <img src="<?=loadImage("assets/images/publicar/plus.png","none")?>" alt="upload">
                        </div>
                    </label>
                    <small class="errors" id="error_imagem">Teste de erro</small>
                </section>

                <script>
                    <?php // Already set a number of attempts for the currently number of images ?>
                    let attempts = <?=count($row['images'])?>; // Number of upload image attempts
                    let images = {}; // Object to store the images
                    const button = document.querySelector(".criacao-upload-button"); // The Upload Button
                    const holder = document.querySelector(".criacao-upload"); // The holder
                    
                    <?php //Storing the database images in the images variable ?>
                    <?php if (!empty($row['images'])) : ?>
                        <?php $i=1; foreach ($row['images'] as $image) : ?>
                            images['<?="img_num".$i?>'] = '<?=$image?>';
                        <?php $i++; endforeach ; ?>
                        fix_button();
                    <?php endif; ?>

                    // Function to dispay the images in the page
                    const display_images = function(files) {

                        const error_field = document.querySelector("#error_imagem");
                        error_field.style.display = "none";
                        error_field.innerHTML = "";

                        // Limit the upload to the maximum of 5 images
                        if (attempts >= 5 || parseInt(attempts) + parseInt(files.length) > 5) {
                            error_field.style.display = "unset";
                            error_field.innerHTML = "Limite de 5 imagens";
                            return;
                        }

                        const allowed = ['jpg','jpeg','png','webp']; // Allowed file extensions
                        let ext = ''; // Extension variable
                        
                        // Processing the images
                        for (let i = 0; i < files.length; i++) {

                            ext = files[i].name.split('.').pop(); // Getting the extension

                            // Checking the extension
                            if (!allowed.includes(ext.toLowerCase())) {
                                document.querySelector("#input_file").value = null;
                                error_field.style.display = "unset";
                                error_field.innerHTML = "Formato de arquivo não suportado";
                                return;
                            }
                            
                            // Creating the image element to display
                            const div = document.createElement('div');
                            div.setAttribute('class','criacao-upload-image');

                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(files[i]);

                            const del = document.createElement('img');
                            del.src = "<?=ROOT?>/assets/images/publicar/remove.png";
                            del.id = "delete-img";
                            del.setAttribute('alt','delete');
                            del.setAttribute('onclick','delete_image(this)');
                            del.setAttribute('deleteID',(Math.random() + 1).toString(36).substring(2));

                            // Merging the elements
                            div.appendChild(img);
                            div.appendChild(del);

                            holder.insertBefore(div,button); // Displaying the image

                            images[del.getAttribute('deleteID')] = files[i];

                            attempts++; // Increasing the number of tries
                        }
                        
                        // Fixing the buttton
                        fix_button();

                        document.querySelector("#input_file").value = null; // Cleaning the input
                        document.querySelector("#input_file").files = null; // Cleaning the input
                    }

                    // Function to fix the add image button position
                    function fix_button() {
                        if (attempts > 0) {
                            holder.style.justifyContent = "unset";
                            button.style.display = "flex";
                        } else {
                            holder.style.justifyContent = "center";
                            button.style.display = "flex";
                        }
                        if (attempts == 5) {
                            holder.style.justifyContent = "space-around";
                            button.style.display = "none";
                        }
                    }

                    // function to delete a image to be uploaded
                    const delete_image = function (img) {
                        img.parentNode.remove(); // Removing the image displayed
                        attempts--; // Descreasing the attempts

                        // Fixing the buttton
                        if (attempts > 0) {
                            holder.style.justifyContent = "unset";
                            button.style.display = "flex";
                        } else {
                            holder.style.justifyContent = "center";
                            button.style.display = "flex";
                        }

                        delete images[img.getAttribute('deleteID')]; // Deleting from the images to be uploaded
                    }

                    // Function do submit the form
                    const submit_form = function (e) {
                        e.preventDefault(); // Preventing the form to be uploaded in the normal way
                        
                        // Getting all the current form data
                        const form_data = document.querySelector(".criacao-form"); 
                        const form = new FormData(form_data);

                        // Adding the images to the form data
                        let i = 1;
                        for (key in images) {
                            form.append("image"+i,images[key]);
                            i++;
                        }

                        ajax = new XMLHttpRequest; // AJAX
                        const loading = document.querySelector(".criacao-overlay");

                        // Handling the result of ajax request
                        ajax.addEventListener('readystatechange', function (e) {
                            if (ajax.readyState == 4 && ajax.status == 200) {
                                loading.style.display = "none";
                                clean_errors();
                                handle_result(ajax.response);
                            }  
                        });

                        // Sending the data
                        ajax.open('post','',true); 
                        ajax.send(form);
                        loading.style.display = "flex";
                    }

                    // Function to handle the ajax result
                    const handle_result = function(result) {
                        console.log(result);
                        result = JSON.parse(result); // Result is received in JSON format
                        if (result['errors']) { 
                            // Displaying the errors if occurred
                            delete result.errors;
                            for (key in result) {
                                let field;
                                if (key == 'imagem1' || key == 'imagem2' || key == 'imagem3' || key == 'imagem4' || key == 'imagem5') {
                                    field = document.querySelector("#error_imagem");
                                } else {  
                                    field = document.querySelector("#error_"+key);
                                }
                                field.style.display = "unset";
                                field.innerHTML = result[key];
                            }

                            const errors = document.querySelectorAll('.errors');
                            for (let i = 0; i < errors.length; i++) {
                                if (errors[i].innerHTML !== "") {
                                    errors[i].scrollIntoView({behavior: "smooth"});
                                    break;
                                }
                            }
                        } else {
                            // Redirecting to another page if no errors and success
                            if (result['success']) {
                                window.location.href = "<?=ROOT?>/moradia/id/"+result['id'];
                            } else {
                                alert("Algo deu errado tente novamente mais tarde");
                                location.reload();
                            }
                        }
                    }

                    // Function to clean the errors displayed
                    const clean_errors = function () {
                        const errors = document.querySelectorAll('.errors');
                        for (let i = 0; i < errors.length; i++) {
                            errors[i].style.display = "none";
                            errors[i].innerHTML = "";
                        }
                    }
                </script>

                <section class="criacao-dados">

                    <h2>DADOS</h2>

                    <label for="input_nome">Nome:</label>
                    <input type="text" name="nome" id="input_nome" value="<?=$row['nome']?>">
                    <small class="errors" id="error_nome"></small>

                    <label for="input_tipo">Tipo: </label>
                    <select name="tipo" id="input_tipo">
                        <?php foreach ($types as $type) : ?>

                            <?php if ($type == $row['tipo']) : ?>
                                <option value="<?=$type?>" <?="selected" ?>><?=esc($type)?></option>
                            <?php continue; endif ; ?>

                            <option value="<?=$type?>"><?=esc($type)?></option>

                        <?php endforeach ;?>
                    </select>
                    <small class="errors" id="error_tipo"></small>

                    <label for="input_cep">CEP: </label>
                    <input type="text" name="cep" id="input_cep" onInput="this.value = formatCep(this.value)" value="<?=formatCEP($row['cep'])?>">
                    <small class="errors" id="error_cep"></small>

                    <label for="input_cidade">Cidade: </label>
                    <input type="text" name="cidade" id="input_cidade" value="<?=$row['cidade']?>">
                    <small class="errors" id="error_cidade"></small>

                    <label for="input_estado">Estado: </label>
                    <select name="uf" id="input_estado">
                        <?php foreach ($states as $state) : ?>

                            <?php if ($state == $row['uf']) : ?>
                                <option value="<?=$state?>" <?="selected"?>><?=esc($state)?></option>
                            <?php continue; endif ; ?>

                            <option value="<?=$state?>"><?=esc($state)?></option>

                        <?php endforeach ;?>
                    </select>
                    <small class="errors" id="error_uf"></small>

                    <label for="input_rua">Rua: </label>
                    <input type="text" name="logradouro" id="input_rua" value="<?=$row['logradouro']?>">
                    <small class="errors" id="error_logradouro"></small>

                    <label for="input_bairro">Bairro: </label>
                    <input type="text" name="bairro" id="input_bairro" value="<?=$row['bairro']?>">
                    <small class="errors" id="error_bairro"></small>

                    <label for="input_numero">Nº da moradia: </label>
                    <input type="text" name="numero" id="input_numero" oninput="formatNum(this)" maxlength = "3" value="<?=$row['numero']?>">
                    <small class="errors" id="error_numero"></small>

                    <label for="input_largura">Largura (em metros): </label>
                    <input type="text" name="largura" id="input_largura" oninput="formatNum(this)" maxlength = "5" value="<?=$row['largura']?>">
                    <small class="errors" id="error_largura"></small>

                    <label for="input_comprimento">Comprimento (em metros): </label>
                    <input type="text" name="comprimento" id="input_comprimento" oninput="formatNum(this)" maxlength = "5" value="<?=$row['comprimento']?>">
                    <small class="errors" id="error_comprimento"></small>

                    <label for="input_comodos">Comodos: </label>
                    <input type="text" name="numero_comodos" id="input_comodos" oninput="formatNum(this)" maxlength = "2" value="<?=$row['numero_comodos']?>">
                    <small class="errors" id="error_numero_comodos"></small>

                    <h3>Possui: </h3>

                    <div class="criacao-dados-extras">

                        <input type="checkbox" name="wifi" id="check_wifi" value="1" onclick="changeImg(this)" <?php if ($row['wifi'] == 1) echo 'checked' ?>>
                        <label for="check_wifi"><img src="<?=ROOT?>/assets/images/publicar/Wifi<?php if ($row['wifi'] == 1) echo 'Blue' ?>.png">Wi-fi</label>

                        <input type="checkbox" name="refeicao" id="check_refeicao" value="1" onclick="changeImg(this)" <?php if ($row['refeicao'] == 1) echo 'checked' ?>>
                        <label for="check_refeicao"><img src="<?=ROOT?>/assets/images/publicar/Dinner<?php if ($row['refeicao'] == 1) echo 'Blue' ?>.png">Refeição</label>

                        <input type="checkbox" name="lazer" id="check_lazer" value="1" onclick="changeImg(this)" <?php if ($row['lazer'] == 1) echo 'checked' ?>>
                        <label for="check_lazer"><img src="<?=ROOT?>/assets/images/publicar/Leisure<?php if ($row['lazer'] == 1) echo 'Blue' ?>.png">Área de Lazer</label>

                        <input type="checkbox" name="estacionamento" id="check_estacionamento" value="1" onclick="changeImg(this)" <?php if ($row['estacionamento'] == 1) echo 'checked' ?>>
                        <label for="check_estacionamento"><img src="<?=ROOT?>/assets/images/publicar/Parking<?php if ($row['estacionamento'] == 1) echo 'Blue' ?>.png">Estacionamento</label>

                        <input type="checkbox" name="animais" id="check_animais" value="1" onclick="changeImg(this)" <?php if ($row['animais'] == 1) echo 'checked' ?>>
                        <label for="check_animais"><img src="<?=ROOT?>/assets/images/publicar/Animals<?php if ($row['animais'] == 1) echo 'Blue' ?>.png">Permite Animais</label>

                    </div>

                    <label for="input_valor">Valor: </label>
                    <input type="text" name="preco" id="input_valor" data-max="99999999" value="R$ <?=formatPrice($row['preco'])?>">
                    <small class="errors" id="error_preco"></small>

                    <label for="input_descricao">Descrição Detalhada: </label>
                    <textarea name="descricao" id="input_descricao" cols="0" rows="0"><?=esc($row['descricao'])?></textarea>
                    <small class="errors" id="error_descricao"></small>

                </section>

                <input type="hidden" name="token" value="<?=$ses->get('token')?>">

            </form>

            <button type="submit" class="submit-button" form="criacao-form">Aplicar alterações</button>

        </section>

        <section class="criacao-overlay">
            <div class="criacao-loading">
                <img src="<?=loadImage("assets/images/publicar/loading.gif","none")?>" alt="loading-gif">
            </div>
        </section>
        
    </main>
</body>
<script src="<?=ROOT?>/assets/js/publicar.js"></script>
</html>