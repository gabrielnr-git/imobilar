<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Publicar class
 */
class Publicar
{
    use Controller;

    public function index(){
        $data = [];
        $errors = [];
        $ses = new \Core\Session;
        $req = new \Core\Request;

        // Check if user is logged in
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        if ($req->posted() && $req->post('token') === $ses->get('token')) {
            unset($_POST['token']);

            // Get all uploaded images exists
            $images = [];
            $_POST['imagem1'] = ''; // For error handling
            foreach ($_FILES as $key => $file) {
                $images[$key] = $file;
                $_POST[$key] = $file;
            }

            // Check if there's no more than 5 images
            if (count($images) > 5) {
                echo "Max size: 5";
                die();
            }

            $moradia = new \Model\Moradia;

            // Validating the inputs
            $moradia->validate($req->post(),'insert'); 
            
            $errors = $moradia->getErrors();

            if (empty($errors)) {
                // Desformatting some inputs
                $req->post("cep") !== '' ? $_POST['cep'] = preg_replace('/-/','',$req->post("cep")) : redirect('publicar');
                $req->post("nome") !== '' ? $_POST['nome'] = ucwords($req->post('nome')) : redirect('publicar');
                $req->post("cidade") !== '' ? $_POST['cidade'] = ucwords($req->post('cidade')) : redirect('publicar');
                $req->post("bairro") !== '' ? $_POST['bairro'] = ucwords($req->post('bairro')) : redirect('publicar');
                $req->post("logradouro") !== '' ? $_POST['logradouro'] = ucwords($req->post('logradouro')) : redirect('publicar');
                if ($req->post("preco") !== '') {
                    $_POST['preco'] = preg_replace('/[R\$ ]|\./','',$req->post("preco"));
                    $_POST['preco'] = preg_replace('/,/','.',$req->post("preco"));
                } else redirect('publicar');
                $ses->getUser('id_usuario') !== '' ? $_POST['id_usuario'] = $ses->getUser('id_usuario') : redirect('publicar');
                $_POST['situacao'] = "Em Análise";

                // Adding the images
                if (!empty($images)) {
                    foreach ($images as $key => $image) {
                        $img = new \Core\Image;

                        $_POST[$key] = $img->upload('assets/images/uploads/moradias/',$image);
                        $img->resize($_POST[$key],720);
                    }
                }


                // Inserting the data
                if ($moradia->insert($req->post())) {
                    $id = $moradia->select('id_moradia');
                    $id = end($id);

                    $ses->regenerate();
                    addNotification("Moradia Publicada","Sua moradia foi publicada e agora será verificada pelo administradores. Você pode conferi-la na pagina 'MINHAS PUBLICAÇÕES'",$ses->getUser('id_usuario'),"/moradia/id/".$id);

                    echo json_encode(['success'=>true,'id'=>$id]); 
                    die();
                }
                echo json_encode(['success'=>false]); 
                die();
            } 

            $errors['errors'] = true;
            echo json_encode($errors);
            die();
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['ses'] = $ses;
        $this->view('publicar',$data);
    }

}
