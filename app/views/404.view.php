    <?=$this->view('header',['title'=>'404 - Pagina não encontrada','css'=>'404'])?>

    <section class="wrapper">
        
        <main class="_404">
        
            <section>
                <img src="<?=loadImage("assets/images/404/404.png")?>" alt="">
                <h2>Erro 404 - Página não encontrada</h2>
                <a href="<?=ROOT?>/moradias">VOLTAR PARA O INÍCIO</a>
            </section>

        </main>

    </section>
</body>
<script src="<?=ROOT?>/assets/js/admin.js"></script>
</html>