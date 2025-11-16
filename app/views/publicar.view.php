    <?=$this->view('header',['title'=>'Publicar Moradia','css'=>'publicar'])?>

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
                    <label class="criacao-upload-button">
                        <div for="input_file">
                            <input type="file" id="input_file" accept="image/*" onchange="display_images(this.files)" multiple>
                            <img src="<?=loadImage("assets/images/publicar/plus.png","none")?>" alt="upload" draggable="false">
                        </div>
                    </label>
                    <small class="errors" id="error_imagem"></small>
                </section>

                <script>
                    let attempts = 0; // Number of upload image attempts
                    let images = {}; // Object to store the images
                    const button = document.querySelector(".criacao-upload-button"); // The Upload Button
                    const holder = document.querySelector(".criacao-upload"); // The holder

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
                            del.deleteID = (Math.random() + 1).toString(36).substring(2);

                            // Merging the elements
                            div.appendChild(img);
                            div.appendChild(del);

                            holder.insertBefore(div,button); // Displaying the image

                            images[del.deleteID] = files[i];

                            attempts++; // Increasing the number of tries
                        }
                        
                        // Fixing the buttton
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

                        document.querySelector("#input_file").value = null; // Cleaning the input
                        document.querySelector("#input_file").files = null; // Cleaning the input
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

                        delete images[img.deleteID]; // Deleting from the images to be uploaded
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
                            form.append("imagem"+i,images[key]);
                            i++;
                        }

                        const ajax = new XMLHttpRequest; // AJAX
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
                    <input type="text" name="nome" id="input_nome">
                    <small class="errors" id="error_nome"></small>

                    <label for="input_tipo">Tipo: </label>
                    <select name="tipo" id="input_tipo">
                        <option value=""> - Selecione - </option>
                        <option value="Casa">Casa</option>
                        <option value="Apartamento">Apartamento</option>
                        <option value="Kitnet">Kitnet</option>
                        <option value="República">República</option>
                        <option value="Quarto">Quarto</option>
                        <option value="Quarto Compartilhado">Quarto Compartilhado</option>
                        <option value="Dormitório">Dormitório</option>
                        <option value="Pensão">Pensão</option>
                    </select>
                    <small class="errors" id="error_tipo"></small>

                    <label for="input_cep">CEP: </label>
                    <input type="text" name="cep" id="input_cep" oninput="formatCep(this.value)">
                    <small class="errors" id="error_cep"></small>

                    <label for="input_cidade">Cidade: </label>
                    <input type="text" name="cidade" id="input_cidade">
                    <small class="errors" id="error_cidade"></small>

                    <label for="input_estado">Estado: </label>
                    <select name="uf" id="input_estado">
                        <option value=""> - Selecione - </option>
                        <option value="Acre">Acre</option>
                        <option value="Alagoas">Alagoas</option>
                        <option value="Amapá">Amapá</option>
                        <option value="Amazonas">Amazonas</option>
                        <option value="Bahia">Bahia</option>
                        <option value="Ceará">Ceará</option>
                        <option value="Distrito Federal">Distrito Federal</option>
                        <option value="Espírito Santo">Espírito Santo</option>
                        <option value="Goiás">Goiás</option>
                        <option value="Maranhão">Maranhão</option>
                        <option value="Mato Grosso">Mato Grosso</option>
                        <option value="Mato Grosso do Sul">Mato Grosso do Sul</option>
                        <option value="Minas Gerais">Minas Gerais</option>
                        <option value="Pará">Pará</option>
                        <option value="Paraíba">Paraíba</option>
                        <option value="Paraná">Paraná</option>
                        <option value="Pernambuco">Pernambuco</option>
                        <option value="Piauí">Piauí</option>
                        <option value="Rio de Janeiro">Rio de Janeiro</option>
                        <option value="Rio Grande do Norte">Rio Grande do Norte</option>
                        <option value="Rio Grande do Sul">Rio Grande do Sul</option>
                        <option value="Rondônia">Rondônia</option>
                        <option value="Roraima">Roraima</option>
                        <option value="Santa Catarina">Santa Catarina</option>
                        <option value="São Paulo">São Paulo</option>
                        <option value="Sergipe">Sergipe</option>
                        <option value="Tocantins">Tocantins</option>
                    </select>
                    <small class="errors" id="error_uf"></small>

                    <label for="input_rua">Rua: </label>
                    <input type="text" name="logradouro" id="input_rua">
                    <small class="errors" id="error_logradouro"></small>

                    <label for="input_bairro">Bairro: </label>
                    <input type="text" name="bairro" id="input_bairro">
                    <small class="errors" id="error_bairro"></small>

                    <label for="input_numero">Nº da moradia: </label>
                    <input type="number" name="numero" id="input_numero" oninput="formatNum(this)" maxLength = 3>
                    <small class="errors" id="error_numero"></small>

                    <label for="input_largura">Largura (em metros): </label>
                    <input type="number" name="largura" id="input_largura" oninput="formatNum(this)" maxLength = 5>
                    <small class="errors" id="error_largura"></small>

                    <label for="input_comprimento">Comprimento (em metros): </label>
                    <input type="number" name="comprimento" id="input_comprimento" oninput="formatNum(this)" maxLength = 5>
                    <small class="errors" id="error_comprimento"></small>

                    <label for="input_comodos">Comodos: </label>
                    <input type="number" name="numero_comodos" id="input_comodos" oninput="formatNum(this)" maxLength = 2>
                    <small class="errors" id="error_numero_comodos"></small>

                    <h3>Possui: </h3>

                    <div class="criacao-dados-extras">

                        <input type="checkbox" name="wifi" id="check_wifi" value="1" onclick="changeImg(this)" alt="" draggable="false">
                        <label for="check_wifi"><img src="<?=loadImage("assets/images/publicar/Wifi.png","none")?>">Wi-fi</label>

                        <input type="checkbox" name="refeicao" id="check_refeicao" value="1" onclick="changeImg(this)" alt="" draggable="false">
                        <label for="check_refeicao"><img src="<?=loadImage("assets/images/publicar/Dinner.png","none")?>">Refeição</label>

                        <input type="checkbox" name="lazer" id="check_lazer" value="1" onclick="changeImg(this)" alt="" draggable="false">
                        <label for="check_lazer"><img src="<?=loadImage("assets/images/publicar/Leisure.png","none")?>" alt="" draggable="false">Área de Lazer</label>

                        <input type="checkbox" name="estacionamento" id="check_estacionamento" value="1" onclick="changeImg(this)" alt="" draggable="false">
                        <label for="check_estacionamento"><img src="<?=loadImage("assets/images/publicar/Parking.png","none")?>">Estacionamento</label>

                        <input type="checkbox" name="animais" id="check_animais" value="1" onclick="changeImg(this)" alt="" draggable="false">
                        <label for="check_animais"><img src="<?=loadImage("assets/images/publicar/Animals.png","none")?>">Permite Animais</label>

                    </div>

                    <label for="input_valor">Valor: </label>
                    <input type="text" name="preco" id="input_valor" oninput="formatCurrency(this,99999999)">
                    <small class="errors" id="error_preco"></small>

                    <label for="input_descricao">Descrição Detalhada: </label>
                    <textarea name="descricao" id="input_descricao" cols="0" rows="0"></textarea>
                    <small class="errors" id="error_descricao"></small>

                </section>

                <input type="hidden" name="token" value="<?=$ses->get('token')?>">

            </form>

            <button type="submit" class="submit-button" form="criacao-form">Publicar</button>

        </section>
        <section class="criacao-overlay">
            <div class="criacao-loading">
                <img src="<?=loadImage("assets/images/publicar/loading.gif","none")?>" alt="loading-gif" draggable="false">
            </div>
        </section>
    </main>    
</body>
<script src="<?=ROOT?>/assets/js/publicar.js"></script>
</html>