<?php

namespace Controller;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Moradias class
 */
class Moradias
{
    use Controller;

    public function index(){
        $data = [];
        $data['rows'] = false;
        $req = new \Core\Request;
        $ses = new \Core\Session;
        $ses->pop('alterar'); // For forgot password
        $ses->pop('active'); // For 2FA
        $filter_settings = $this->filter($ses->get('filter_settings',[])); // filter setting
        $params = $filter_settings['params'] ?? []; // the params to query
        $values = !empty($params) ? $filter_settings['values'] : []; // the values of the params 
        $params['situacao'] = "situacao = 'Aprovado'"; // default param
        $categories = ['casa','apartamento','kitnet','república','quarto','quarto_compartilhado','dormitório','pensão'];
        $cities = [];
        $states = [];
        $errors = [];

        $moradia = new \Model\Moradia;

        // Getting the cities
        $moradia->setOrder(["cidade"=>"ASC"]);
        $cities = $moradia->selectAll("cidade",['situacao'=>'Aprovado']);
        foreach ($cities as $key => $value) {
            $city = strtolower($value['cidade']);
            $city = ucwords($city);
            $cities[$city] = $city;
            unset($cities[$key]);
        }

        // Getting the states
        $moradia->setOrder(["uf"=>"ASC"]);
        $states = $moradia->selectAll("uf",['situacao'=>'Aprovado']);
        foreach ($states as $key => $value) {
            $state = strtolower($value['uf']);
            $state = ucwords($state);
            $states[$state] = $state;
            unset($states[$key]);
        }

        $moradia->setOrder([],true); // Reseting the order

        // POST
        if ($req->posted() && $req->post('token') === $ses->pop('token')) {
            unset($_POST['token']);
            $filtro = new \Model\Filtro;
            if ($filtro->validate($req->post(),'update')){
                $ses->set('filter_settings',$req->post());
                redirect('moradias');
                die();
            }
            $errors = $filtro->getErrors();
        }

        $total = $moradia->manualCount("id_moradia",$params,$values);
        $max = 10;

        $pager = new \Core\Pager($total,$max);
        $moradia->setOffset($pager->getOffset());
        $moradia->setLimit($max);

        // Selecting the rows
        $rows = $moradia->manualSelect("*",$params,$values);
        if ($rows){
            $usuario = new \Model\Usuario;
            $favoritos = new \Model\Favoritos;
            $favorites = $favoritos->selectAll('id_moradia',['id_usuario'=>$ses->getUser('id_usuario')]);
            foreach ($favorites as $key => $value) {
                $favorites[$value['id_moradia']] = 'Red';
                unset($favorites[$key]);
            }
            foreach ($rows as &$row) {
                $row_user = $usuario->select("email,email_contato,telefone",['id_usuario'=>$row['id_usuario']]);
                $row = array_merge($row,$row_user);
                unset($row['imagem2']);
                unset($row['imagem3']);
                unset($row['imagem4']);
                unset($row['imagem5']);
                unset($row['email_administrador']);
            }
            $data['rows'] = $rows;
            $data['favorites'] = $favorites;
        }

        $ses->set('token',bin2hex(random_bytes(16))); // CSRF token

        $data['cities'] = $cities;
        $data['states'] = $states;
        $data['categories'] = $categories;
        $data['filter_settings'] = $filter_settings['settings'] ?? [];
        $data['errors'] = $errors;
        $data['pager'] = $pager;
        $data['ses'] = $ses;
        $this->view('moradias',$data);
    }

    // Function to setup the filter settings
    private function filter(array $data) : array
    {
        $params = [];
        $values = [];
        $settings = [];
        $categories = ['casa','apartamento','kitnet','república','quarto','quarto_compartilhado','dormitório','pensão'];
        $equal = ['cep','cidade','uf','numero_comodos'];
        $extras = ['wifi','refeicao','lazer','estacionamento','animais'];
        foreach ($equal as $value) {
            if (isset($data[$value]) && !empty($data[$value])) {
                $params[$value] = $value . " = ?";
                $values[] = preg_replace("/-/","",$data[$value]);
                $settings[$value] = $data[$value];
            }
        }
        foreach ($extras as $value) {
            if (isset($data[$value]) && $data[$value]) {
                $params[$value] = $value . " = 1";
                $settings[$value] = 1;
            }
        }

        foreach ($categories as $value) {
            if (isset($data[$value]) && $data[$value]) {
                $params['tipo'][] = "tipo = ?";
                $values[] = ucwords(preg_replace('/_/',' ',$value));
                $settings[$value] = 1;
            }
        }
        if (isset($params['tipo']) && !empty($params['tipo'])){
            $params['tipo'] = implode(" || ",$params['tipo']);
            $params['tipo'] = "(" . $params['tipo'] . ")";
        } 

        if (isset($data['preco_min']) && !empty($data['preco_min'])) {
            $price = preg_replace("/[R\$ ]|\./","",$data['preco_min']);
            $price = preg_replace("/,/",".",$price);
            $params['preco_min'] = "preco >= ?";
            $values[] = $price;
            $settings['preco_min'] = $data['preco_min'];
        }
        if (isset($data['preco_max']) && !empty($data['preco_max'])) {
            $price = preg_replace("/[R\$ ]|\./","",$data['preco_max']);
            $price = preg_replace("/,/",".",$price);
            $params['preco_max'] = "preco <= ?";
            $values[] = $price;
            $settings['preco_max'] = $data['preco_max'];
        }
        return ['params'=>$params,'values'=>$values,'settings'=>$settings];
    }

    // Function to favorite a moradia
    public function favorite()
    {
        $req = new \Core\Request;
        $ses = new \Core\Session;
        if (!$req->posted() || !$ses->is_logged() || $req->post('token') !== $ses->get("token")) {
            redirect("moradias");
            echo json_encode(['success'=>false]);
            die();
        }

        $favoritos = new \Model\Favoritos;

        $data = ['id_moradia'=>$req->post('id_moradia'),'id_usuario'=>$ses->getUser('id_usuario')];

        if ($favoritos->count("*",$data) == 1) {
            $favoritos->delete($data);
            echo json_encode(['success'=>true,'mode'=>'delete']);
            die();
        }

        if ($favoritos->insert($data)){
            echo json_encode(['success'=>true,'mode'=>'add']);
            die();
        };
    }

    // Function to reser filter setting
    public function reset()
    {
        $ses = new \Core\Session;
        $ses->pop('filter_settings');
        redirect('moradias');
    }
}
