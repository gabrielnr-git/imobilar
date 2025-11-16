    <?=$this->view('header',['title'=>'Notificações','css'=>'notificacoes'])?>

    <section class="wrapper">
        <main class="notifications">

            <?php if ($rows) : ?>

                <section class="notifications-header">
                    <h1><img src="<?=loadImage("assets/images/notificacoes/bell.png","none")?>" alt="">Suas Notificações:</h1>
                    <div>
                        <button onclick="readAll()">Marcar todas como lidas</button>
                        <button onclick="removeAll('readed')">Remover lidas</button>
                        <button onclick="removeAll('all')">Remover todas</button>
                    </div>
                </section>

                <section class="notifications-content">

                    <?php foreach ($rows as $row) : ?>
                        <div class="notification-content-item <?=$row['lido'] == 0 ? 'notification-content-item-unreaded' : '' ;?>">
                            <h2><?=esc($row['assunto'])?></h2>
                            <div>
                                <p><?=esc($row['conteudo'])?></p>
                                <?php if ($row['lido'] == 0) : ?>
                                    <span>(<?=$row['days'] <= 0 ? 'Hoje' : "Há ".$row['days']." dias atrás";?>)</span>
                                <?php elseif ($row['lido'] == 1) : ?>
                                    <span>(Lido <?=$row['days'] <= 0 ? 'Hoje' : "há ".$row['days']." dias atrás";?>)</span>
                                <?php endif ; ?>
                            </div>
                            <div>
                                <?php if (!empty($row['link'])) : ?>
                                    <a href="<?=ROOT . $row['link']?>">Link</a>
                                <?php endif ; ?>
                                <?php if ($row['lido'] == 0) : ?>
                                    <button onclick="read(<?=$row['id_notificacao']?>,this)">Marcar como lida</button>
                                    <small>Notificacoes não lidas são deletadas após 30 dias</small>
                                <?php elseif ($row['lido'] == 1) : ?>
                                    <button onclick="remove(<?=$row['id_notificacao']?>,this)">Remover</button>
                                    <small>Notificacoes lidas são deletadas após 7 dias</small>
                                <?php endif ; ?>
                            </div>
                        </div>
                    <?php endforeach ; ?>

                    <?=$pager->display()?>

                    <script>
                        const read = function (id,element) {
                            const form = new FormData();

                            form.append('id_notificacao',id);
                            form.append('token','<?=$ses->get('token')?>');

                            const ajax = new XMLHttpRequest;

                            ajax.addEventListener('readystatechange',function (e) {
                                if (ajax.readyState == 4 && ajax.status == 200) {
                                    if (ajax.responseText) {
                                        element.innerHTML = "Remover";
                                        element.setAttribute("onclick","remove("+id+",this)");

                                        const content = element.parentNode.parentNode;
                                        content.style.backgroundColor = "white";
                                        content.querySelector("div span").innerHTML = "(Hoje)";
                                        content.querySelector("small").innerHTML = "Notificacoes lidas são deletadas após 7 dias";
                                    } else {
                                        alert("Algo deu errado!");
                                    }
                                }
                            });

                            ajax.open('post','<?=ROOT?>/notificacoes/read',true);
                            ajax.send(form);
                        }

                        const readAll = function () {
                            if (confirm("Marcar todas como lida?")) {
                                const form = new FormData();

                                form.append('token','<?=$ses->get('token')?>');

                                const ajax = new XMLHttpRequest;

                                ajax.addEventListener('readystatechange',function (e) {
                                    if (ajax.readyState == 4 && ajax.status == 200) {
                                        if (ajax.responseText == true) {
                                            location.reload();
                                        } else {
                                            alert("Algo deu errado!");
                                        }
                                    }
                                });

                                ajax.open('post','<?=ROOT?>/notificacoes/readAll',true);
                                ajax.send(form);
                            }
                        }

                        const remove = function (id,element) {
                            const form = new FormData();

                            form.append('id_notificacao',id);
                            form.append('token','<?=$ses->get('token')?>');

                            const ajax = new XMLHttpRequest;

                            ajax.addEventListener('readystatechange',function (e) {
                                if (ajax.readyState == 4 && ajax.status == 200) {
                                    if (ajax.responseText == true) {
                                        element.parentNode.parentNode.remove();
                                        const total = document.querySelectorAll(".notification-content-item").length;
                                        if (total <= 0) {
                                            location.reload();
                                        }
                                    } else {
                                        alert("Algo deu errado!");
                                    }
                                }
                            });

                            ajax.open('post','<?=ROOT?>/notificacoes/remove',true);
                            ajax.send(form);
                        }

                        const removeAll = function (action) {
                            let message = "";
                            if (action == 'all') {message = "Remover todas as notificações?";}
                            if (action == 'readed') {message = "Remover todas as notificações lidas?";}
                            if (confirm(message)) {
                                const form = new FormData();

                                form.append('action',action);
                                form.append('token','<?=$ses->get('token')?>');

                                const ajax = new XMLHttpRequest;

                                ajax.addEventListener('readystatechange',function (e) {
                                    if (ajax.readyState == 4 && ajax.status == 200) {
                                        if (ajax.responseText == true) {
                                            location.reload();
                                        } else {
                                            alert("Algo deu errado!");
                                        }
                                    }
                                });

                                ajax.open('post','<?=ROOT?>/notificacoes/removeAll',true);
                                ajax.send(form);
                            }
                        }
                    </script>

                </section>

            <?php else : ?>
                <section class="notifications-dhave">
                    <img src="<?=loadImage("assets/images/notificacoes/no-notif.png",'none')?>" alt="">
                    <h2>Você não possui notificações neste momento</h2>
                    <a href="<?=ROOT?>/moradias">RETORNAR AO INÍCIO</a>
                </section>
            <?php endif ; ?>

        </main>
    </section>
</body>
<script src="<?=ROOT?>/assets/js/notificacoes.js"></script>
</html>