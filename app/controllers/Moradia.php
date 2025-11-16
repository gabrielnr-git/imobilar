<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Moradia class
 */
class Moradia
{
    use Controller;

    public function index(){
        redirect('moradias');
    }

    public function id($id = 0)
    {
        $data = [];
        $ses = new \Core\Session;

        if ($id <= 0) {
            redirect('moradias');
            die();
        }

        // Getting moradia of given id
        $data['row'] = false;
        $moradia = new \Model\Moradia;
        $usuario = new \Model\Usuario;
        $row_moradia = $moradia->select("*",['id_moradia'=>$id]);
        $row_usuario = $row_moradia ? $usuario->select("nome_completo,nome_usuario,email,email_contato,telefone",['id_usuario'=>$row_moradia['id_usuario']]) : false;

        if ($row_moradia && $row_usuario) {
            $row = array_merge($row_moradia,$row_usuario);
            unset(
                $row['email_administrador'],
            );
            $data['row'] = $row;
        }

        $ses->set('token', bin2hex(random_bytes(16))); // CSRF token

        $data['ses'] = $ses;
        $this->view('moradia',$data);
    }

    public function delete()
    {
        $ses = new \Core\Session;
        $req = new \Core\Request;

        // Check if user is logged
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }

        // Check for post request
        if (!$req->posted() || $req->post('token') !== $ses->pop('token')) {
            redirect('moradias');
            die();
        }

        $id_moradia = $req->post('id');
        $moradia = new \Model\Moradia;

        // getting the owner
        $owner = $moradia->select('id_usuario,imagem1,imagem2,imagem3,imagem4,imagem5',['id_moradia'=>$id_moradia]);
        if (!$owner) die();

        if (!($ses->is_admin() && $ses->get('confirmation',false))) {
            if ($owner['id_usuario'] != $ses->getUser('id_usuario')) {
                echo json_encode(['success'=>false,'message'=>'Você não é o proprietario dessa moradia']);
                die();
            }
        }

        // Deleting
        if ($moradia->delete(['id_moradia'=>$id_moradia])){
            unset($owner['id_usuario']);
            foreach ($owner as $image) if (file_exists($image)) unlink($image);
            message("Moradia deletada com sucesso");
            echo json_encode(['success'=>true]);
        } else{
            echo json_encode(['success'=>false,'message'=>'Algo deu errado tente novamente mais tarde']);
        }
    }

    public function edit($id = 0)
    {
        $data = [];
        $id = intval($id);
        $ses = new \Core\Session;
        $req = new \Core\Request; 

        // Check if the user is logged
        if (!$ses->is_logged()) {
            redirect('login');
            die();
        }
        // Check if the id is a int
        if ($id <= 0 || !is_int($id)) {
            redirect('moradias');
            die();
        }

        // Getting the models
        $moradia = new \Model\Moradia;
        $usuario = new \Model\Usuario;

        // Selecting the row to edit
        $row = $moradia->select("*",['id_moradia'=>$id]);

        if (!$row) {
            redirect('moradias');
            die();
        }

        // Putting all the images in a array
        $row['images'] = [];
        for ($i=1; $i <= 5; $i++) { 
            if (isset($row['imagem'.$i]) && !empty($row['imagem'.$i])) {
                $row['images'][$i] = $row['imagem'.$i];
                unset($row['imagem'.$i]);
            }
        }
        // Unsetting undesired data
        unset(
            $row['email_administrador'],
            $row['id_extras']
        );

        // Check if the user belongs the row
        if (!($ses->is_admin() && $ses->get('confirmation',false))) {
            if ($row['id_usuario'] != $ses->getUser('id_usuario')) {
                redirect('moradias');
                die();
            }
        }

        // ----------------------------------------------------------------------------- //
        // POST START
        if ($req->posted() && $req->post('token') === $ses->get('token')) {
            unset($_POST['token']);
            $errors = [];
            $images = [];

            $missing = 0; // Count missing images
            // Images that come from POST are to keep
            for ($i=1; $i <= 5; $i++) { 
                if ($req->post('image'.$i) !== '') {
                    $images['imagem'.$i]['name'] = $req->post('image'.$i);
                    $_POST['imagem'.$i]['name'] = $req->post('image'.$i); // For validation
                    $_POST['imagem'.$i]['size'] = 1; // For validation
                    $_POST['imagem'.$i]['type'] = 'image/jpeg'; // For validation
                    unset($_POST['image'.$i]);
                } else {
                    $images['imagem'.$i] = null;
                    $_POST['imagem'.$i] = null;
                    $missing++;
                }
            }
            // Images from FILES are new ones to store
            if ($req->files() !== '' && count($req->files()) <= $missing){
                for ($i=(6-$missing); $i <= 5; $i++) { 
                    $images['imagem'.$i] = $req->files('image'.$i); 
                    $_POST['imagem'.$i] = $req->files('image'.$i); // For validation
                }
            }
            // Limit Error
            if(count($req->files()) > $missing) $errors['imagem'] = 'Limite de 5 imagens'; 
            unset($missing);

            // Validation of checkboxes
            $extras = [
                'wifi'=>0,
                'refeicao'=>0,
                'lazer'=>0,
                'estacionamento'=>0,
                'animais'=>0,
            ];
            foreach ($extras as $key => $value) {
                $_POST[$key] = $_POST[$key] ?? $value;
            }

            // Check contact data
            if ($req->post('telefone') == $ses->getUser('telefone')) unset($_POST['telefone']);
            if ($req->post('email_contato') == $ses->getUser('email_contato')) unset($_POST['email_contato']);

            // Validation
            $moradia->validate($req->post(),'update');
            $usuario->validate($req->post(),'update');

            $errors = array_merge($moradia->getErrors(),$usuario->getErrors()); // Merging the errors

            // Show the errors
            if (!empty($errors)) {
                $errors['errors'] = true;
                echo json_encode($errors);
                die();
            }

            // Desformatting some inputs
            $req->post("cep") !== '' ? $_POST['cep'] = preg_replace('/-/','',$req->post("cep")) : redirect('publicacoes');
            $req->post("nome") !== '' ? $_POST['nome'] = ucwords($req->post('nome')) : redirect('publicacoes');
            $req->post("cidade") !== '' ? $_POST['cidade'] = ucwords($req->post('cidade')) : redirect('publicacoes');
            $req->post("bairro") !== '' ? $_POST['bairro'] = ucwords($req->post('bairro')) : redirect('publicacoes');
            $req->post("logradouro") !== '' ? $_POST['logradouro'] = ucwords($req->post('logradouro')) : redirect('publicacoes');
            if ($req->post("preco") !== '') {
                $_POST['preco'] = preg_replace('/[R\$ ]|\./','',$req->post("preco"));
                $_POST['preco'] = preg_replace('/,/','.',$req->post("preco"));
            } else redirect('publicacoes');
            $_POST['situacao'] = "Em Análise";

            // Unsetting if inputs are the same as before
            foreach ($req->post() as $key => $value) {
                if (isset($row[$key]) && $row[$key] == $value) unset($_POST[$key]);
            }

            // Getting the images to unlink
            $unlink = [1=>1,2=>2,3=>3,4=>4,5=>5];
            foreach ($row['images'] as $key => $db_image) {
                foreach ($images as $image) {
                    if (isset($image['name']) && $image['name'] == $db_image){
                        unset($unlink[$key]);
                    }
                }
            }

            // Unlinking the images
            foreach ($unlink as $value) {
                if (isset($row['images'][$value]) && file_exists($row['images'][$value])) unlink($row['images'][$value]); 
            }

            $img = new \Core\Image;

            // Uploading the new images
            foreach ($images as $key => &$image) {
                if (isset($image['tmp_name'])) {
                    $image = $img->upload("assets/images/uploads/moradias/",$image);
                    $img->resize($image,720);
                    $_POST[$key] = $image;
                } else $_POST[$key] = $image['name'] ?? null;
            }

            // Updating the database
            if ($moradia->update($req->post(),['id_moradia'=>$id])){
                addNotification("Moradia Atualizada","Uma de suas moradias foi atualizada!",$ses->getUser("id_usuario"),"/moradia/id/".$id);
                echo json_encode(['success'=>true,'id'=>$id]);
                die();
            }

            echo json_encode(['success'=>false]); // Fail
            die();
        }
        // POST END
        // ----------------------------------------------------------------------------- //

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF Token

        $data['row'] = $row;
        $data['states'] = [
            'Acre', 'Alagoas', 'Amapá', 'Amazonas', 'Bahia', 'Ceará', 'Distrito Federal',
            'Espírito Santo', 'Goiás', 'Maranhão', 'Mato Grosso', 'Mato Grosso do Sul',
            'Minas Gerais', 'Pará', 'Paraíba', 'Paraná', 'Pernambuco', 'Piauí',
            'Rio de Janeiro', 'Rio Grande do Norte', 'Rio Grande do Sul', 'Rondônia',
            'Roraima', 'Santa Catarina', 'São Paulo', 'Sergipe', 'Tocantins'
        ];
        $data['types'] = ['Casa','Apartamento','Kitnet','República','Quarto','Quarto Compartilhado','Dormitório','Pensão'];
        $data['ses'] = $ses;
        $this->view('moradia_edit',$data);
    }
}
