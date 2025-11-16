<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * MainModel class
 * 
 * select - columns, condition, operator
 * selectAll - columns, condition, operator
 * count - column
 * like - columns, like, operator
 * insert - data
 * update - data, condition, operator
 * delete - condition, operator
 */
Trait Model
{
    use Database;

    protected $limit = 0; // Limit for when selecting
    protected $offset = 0; // Offset for when selecting
    protected $errors = [];

    // Select the first row
    public function select(string $columns, array $condition = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $params = [];
        $values = [];
        $order = [];
        $query = "SELECT " . $columns . " FROM " . $this->table;
        if (!empty($condition)){
            
            foreach ($condition as $key => $value) {
                $params[] = $key . " = ?";
                $values[] = $value;
            }
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE " . $params;
        }
        foreach ($this->order as $column => $type) {
            $order[] = $column . " " . $type;
        }
        $order = implode(",",$order);
        $query .= " ORDER BY " . $order;
        if ($this->limit > 0) $query .= " LIMIT 1 OFFSET " . $this->offset;
        $query .= ";";

        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        }
        return false;
    }

    // Select all rows
    public function selectAll(string $columns, array $condition = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $params = [];
        $values = [];
        $query = "SELECT " . $columns . " FROM " . $this->table;
        if (!empty($condition)){
            
            foreach ($condition as $key => $value) {
                $params[] = $key . " = ?";
                $values[] = $value;
            }
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE ". $params;
        } 
        foreach ($this->order as $column => $type) {
            $order[] = $column . " " . $type;
        }
        $order = implode(",",$order);
        $query .= " ORDER BY " . $order;
        if ($this->limit > 0) $query .= " LIMIT " . $this->limit . " OFFSET " . $this->offset;
        $query .= ";";

        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        }
        return false;
    }

    // Select all rows
    public function manualSelect(string $columns, array $params = [], $values = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $matches = 0;
        foreach ($params as $param) $matches += preg_match_all("/\?/",$param);
        if ($matches != count($values)) return false;

        $query = "SELECT " . $columns . " FROM " . $this->table;
        if (!empty($params)){
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE ". $params;
        }
        foreach ($this->order as $column => $type) {
            $order[] = $column . " " . $type;
        }
        $order = implode(",",$order);
        $query .= " ORDER BY " . $order;
        if ($this->limit > 0) $query .= " LIMIT " . $this->limit . " OFFSET " . $this->offset;
        $query .= ";";

        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        }
        return false;
    }

    // Select all rows
    public function like(string $columns, array $like, string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $params = [];
        $values = [];
        $query = "SELECT " . $columns . " FROM " . $this->table;
        foreach ($like as $key => $value) {
            $params[] = $key . " LIKE ?";
            $values[] = $value;
        }
        $params = implode(" " . $op . " ",$params);
        $query .= " WHERE ". $params;

        foreach ($this->order as $column => $type) {
            $order[] = $column . " " . $type;
        }
        $order = implode(",",$order);
        $query .= " ORDER BY " . $order;
        if ($this->limit > 0) $query .= " LIMIT " . $this->limit . " OFFSET " . $this->offset;
        $query .= ";";

        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = null;
            return $result;
        }
        return false;
    }

    // Count all rows based on a column
    public function count(string $column, array $condition = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;
        $params = [];
        $values = [];
        
        $values[] = $column;
        $query = "SELECT COUNT(?) FROM " . $this->table;
        if (!empty($condition)){
            foreach ($condition as $key => $value) {
                $params[] = $key . " = ?";
                $values[] = $value;
            }
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE ". $params;
        } 
        
        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetch(\PDO::FETCH_COLUMN);
            $stmt = null;
            return $result;
        }
        return false;
    }

    public function manualCount(string $column, array $params = [], $values = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $matches = 0;
        foreach ($params as $param) $matches += preg_match_all("/\?/",$param);
        if ($matches != count($values)) return false;

        $values = array_merge([$column],$values);
        $query = "SELECT COUNT(?) FROM " . $this->table;
        if (!empty($params)){
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE ". $params;
        }
        $query .= ";";

        $stmt = $this->query($query,$values);
        if ($stmt) {
            $result = $stmt->fetch(\PDO::FETCH_COLUMN);
            $stmt = null;
            return $result;
        }
        return false;
    }

    // Insert something in the database
    public function insert(array $data){
        $data = $this->cleanData($data);
        if (empty($data)) return true;
        $columns = [];
        $params = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $params[] = "?";
            $values[] = $value;
        }
        $columns = implode(",",$columns);
        $params = implode(" , ",$params);
        $query = "INSERT INTO " . $this->table . " (" . $columns . ") VALUES (" . $params . ");";

        return $this->query($query,$values) ? true : false;
    }

    // Update something in the database
    public function update(array $data, array $condition, string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;
        $data = $this->cleanData($data);
        if (empty($data)) return true;
        $params = [];
        $values = [];
        foreach ($data as $key => $value) {
            $params[] = $key . " = ?";
            $values[] = $value;
        }
        $params = implode(",",$params);
        $query = "UPDATE " . $this->table . " SET " . $params . " WHERE ";

        $params = null;
        foreach ($condition as $key => $value) {
            $params[] = $key . " = ?";
            $values[] = $value;
        }
        $params = implode(" " . $op . " ",$params);
        $query .= $params . ";";

        return $this->query($query,$values) ? true : false;
    }

    // Delete something in the database
    public function delete(array $condition = [], string $op = "&&"){
        if ($op != "&&" && $op != "||") return false;

        $params = [];
        $values = [];
        $query = "DELETE FROM " . $this->table;
        if (!empty($condition)){
            
            foreach ($condition as $key => $value) {
                $params[] = $key . " = ?";
                $values[] = $value;
            }
            $params = implode(" " . $op . " ",$params);
            $query .= " WHERE ". $params;
        } 
        $query .= ";";
        
        return $this->query($query,$values) ? true : false;
    }

    // Clean undesired data
    private function cleanData($data){
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    /**
     * -- Validation Rules --
     * required,
     * unique,
     * no_alpha
     * no_numbers
     * no_symbols
     * no_symbols_
     * no_spaces
     * max_length=X
     * min_length=X
     * email
     * phone_number
     * date
     * datetime
     * price
     * cep
     * image
     * max_size=X
     * in_array=*array*
     * bool
     * ----------------------
     */
    public function validate(array $data, string $mode) : bool
    {
        $validationRules = [];

        // Setup the mode
        if ($mode === "update") {
            $validationRules = $this->onUpdateValidationRules;
        } else if ($mode === "insert"){
            $validationRules = $this->onInsertValidationRules;
        }

        if (!empty($validationRules)) {

            foreach ($validationRules as $column => $rules) {
                if (!isset($data[$column]) || empty($data[$column]) && !in_array('required',$rules)) 
                    continue; // If a column does not exist in the data provided skip all the validation

                // Formating the rules array ($rule) to ($rule => $value)
                foreach ($rules as $key => $rule) {
                    if (preg_match("/\bmax_length=\d+\b/i",$rule)) {
                        $value = explode("=",$rule);
                        $value = end($value);
                        $rules['max_length'] = $value; 
                        $value = null;
                        unset($rules[$key]);
                    } else if (preg_match("/\bmin_length=\d+\b/i",$rule)) {
                        $value = explode("=",$rule);
                        $value = end($value);
                        $rules['min_length'] = $value;
                        $value = null;
                        unset($rules[$key]);
                    } else if (preg_match("/\bmax_size=\d+\b/i",$rule)) {
                        $value = explode("=",$rule);
                        $value = end($value);
                        $rules['max_size'] = $value;
                        $value = null;
                        unset($rules[$key]);
                    } else if (preg_match("/\bin_array=.+\b/i",$rule)) {
                        $value = explode("=",$rule);
                        $value = end($value);
                        $value = explode(',',$value);
                        $rules['in_array'] = $value;
                        $value = null;
                        unset($rules[$key]);
                    } else {
                        $rules[$rule] = true;
                        unset($rules[$key]);
                    }
                }

                // Validating
                foreach ($rules as $rule => $value) {
                    switch ($rule) {
                        case 'required':
                            if (empty($data[$column])){
                                $this->errors[$column] = "Este campo é obrigatório"; 
                                unset($_POST[$column]);
                            }
                        break;

                        case 'unique':
                            if ($this->select($column,[$column=>$data[$column]])){
                                $this->errors[$column] = "Este campo já está sendo usado";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'email':
                            if (!filter_var($data[$column],FILTER_VALIDATE_EMAIL)){
                                $this->errors[$column] = "Este campo precisa ser um email valido";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'phone_number':
                            if (!preg_match("/^\(\d{2}\) \d{5}-\d{4}$/",$data[$column])){
                                $this->errors[$column] = "Sintaxe Invalida. Exemplo de sintaxe: (12) 12345-6789";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'date':
                            if (!preg_match("/^[1-9][0-9]?[0-9]?[0-9]?-(0[1-9]|1[0-2])-([0-2][0-9]|30)$/",$data[$column])){
                                $this->errors[$column] = "Sintaxe Invalida. Exemplo de sintaxe: YYYY-MM-DD";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'datetime':
                            if (!preg_match("/^[1-9][0-9]?[0-9]?[0-9]?-(0[1-9]|1[0-2])-([0-2][0-9]|30) ([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/",$data[$column])){
                                $this->errors[$column] = "Sintaxe Invalida. Exemplo de sintaxe: YYYY-MM-DD";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'price':
                            if (!preg_match("/^R\\$ (\d{1,3}\.\d{3}|\d{1,3}),\d{2}$/",$data[$column])){
                                $this->errors[$column] = "Sintaxe Invalida. Exemplo de sintaxe: R$ 999.999,99";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'cep':
                            if (!preg_match("/^\d{5}-\d{3}$/",$data[$column])){
                                $this->errors[$column] = "Sintaxe Invalida. Exemplo de sintaxe: 12345-678";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'max_length':
                            if (strlen(trim($data[$column])) > $value){
                                $this->errors[$column] = "Este campo precisa ter maximo " . $value . " caracteres";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'min_length':
                            if (strlen(trim($data[$column])) < $value){
                                $this->errors[$column] = "Este campo precisar ter no minimo " . $value . " caracteres";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'no_alpha':
                            if (preg_match("/[a-zA-ZáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙâÂêÊîÎôÔûÛãÃõÕçÇ]/",$data[$column])) {
                                $this->errors[$column] = "Este campo não pode conter letras";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'no_numbers':
                            if (preg_match("/[0-9]/",$data[$column])){ 
                                $this->errors[$column] = "Este campo não pode conter numeros";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'no_symbols':
                            if (preg_match("/[^a-zA-Z0-9áÁéÉíÍóÓúÚàÀèÈìÌòÒùÙâÂêÊîÎôÔûÛãÃõÕçÇ ]/",$data[$column])) {
                                $this->errors[$column] = "Este campo não pode conter simbolos";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'no_symbols_':
                            if (preg_match("/[^a-zA-Z0-9áÁéÉíÍóÓúÚàÀèÈìÌòÒùÙâÂêÊîÎôÔûÛãÃõÕçÇ_ ]/",$data[$column])) {
                                $this->errors[$column] = "Este campo não pode conter simbolos, apenas ' _ '";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'no_spaces':
                            if (preg_match("/\s/",$data[$column])) {
                                $this->errors[$column] = "Este campo não pode conter espaços em branco";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'image':
                            if (isset($data[$column]['name']) && isset($data[$column]['type'])) {
                                $ext = explode(".",$data[$column]['name']);
                                $ext = end($ext);
                                $allowed = ['png','jpeg','jpg','webp'];
                                $mime = ['image/jpeg','image/png','image/webp'];
                                if (!in_array($ext,$allowed) || !in_array($data[$column]['type'],$mime)) {
                                    $this->errors[$column] = "Formato de imagem não suportado";
                                    unset($_POST[$column]);
                                }
                            } else {
                                $this->errors[$column] = "Formato de arquivo não suportado";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'max_size':
                            if (isset($data[$column]['size'])) {
                                if ($data[$column]['size'] > (($value*1024)*1024)) {
                                    $this->errors[$column] = "Limite de tamanho para este arquivo " . $value . "MB";
                                    unset($_POST[$column]); 
                                }
                            } else {
                                $this->errors[$column] = "Formato de arquivo não suportado";
                                unset($_POST[$column]);
                            }
                        break;

                        case 'in_array':
                            if (!in_array($data[$column],$value)) {
                                $this->errors[$column] = "Valor invalido";
                                unset($_POST[$column]); 
                            }
                        break;

                        case 'bool':
                            if ($data[$column] != 0 && $data[$column] != 1) {
                                $this->errors[$column] = "Valor invalido";
                                unset($_POST[$column]); 
                            }
                        break;
                        
                        default:
                            $this->errors['rules'] = "The rule ". $rule . " does not exist!";
                            break;
                    }
                }
            }
        }
        // If no error return true
        if (empty($this->errors)) {
            return true;
        }
        // Return false otherwise
        return false;
    }

    // Return the errors
    public function getErrors(string $key = ''){
        if (!empty($key)) return $this->errors[$key] ?? ""; 
        return $this->errors;
    }

    // Set the limit
    public function setLimit(int $value){
        $this->limit = $value;
    }

    // Set the offset
    public function setOffset(int $value){
        $this->offset = $value;
    }
}
