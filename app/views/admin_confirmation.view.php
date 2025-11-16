    <?=$this->view('header',['title'=>'Imobilar','css'=>'admin'])?>
    
    <section class="wrapper">
        <main class="confirmation">
            <form method="post" class="confirmation-form">
                <h2>Confirme sua identidade:</h2>
                <div class="confirmation-inputs">
                    <div>
                        <input type="password" name="pwd" placeholder="Digite sua senha">
                        <img src="<?=ROOT?>/assets/images/admin/show.png" alt="mostrar senha" onclick="togglePassword(this)" required>
                    </div>
                    <div>
                        <input type="password" name="pwd_confirmation" placeholder="Confirme sua senha">
                        <img src="<?=ROOT?>/assets/images/admin/show.png" alt="mostrar senha" onclick="togglePassword(this)" required>
                    </div>
                </div>
                <input type="hidden" name="token" value="<?=$ses->get('token')?>">
                <?php if (isset($errors['inputs'])) : ?>
                    <small class="errors"><?=$errors['inputs']?></small>
                <?php endif ; ?>
                <button type="submit">Confirmar</button>
            </form>
        </main>
    </section>
    
</body>
<script src="<?=ROOT?>/assets/js/admin.js"></script>
</html>
