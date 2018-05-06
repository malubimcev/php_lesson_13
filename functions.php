<?php
    function get_empty_recordset()//возвращает пустой набор
    {
        $recordset = [
            [
              'description' => '-',
              'date_added' => '-',
              'is_done' => '-'
            ]
        ];
        return $recordset;
    }
    
    function get_connection()//создаем и возвращаем объект PDO
    {
        require_once 'config.php';//подключение файла конфигурации параметров соединения
        try {
            $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            return $pdo;
        } catch (Exception $error) {
            return NULL;
        }
    }
    
    function do_command(&$params)
    {
        $recset = [];//массив для набора записей
        $request_params = [];//массив для параметров запросов
        $tmp_params = [];//временный массив для параметров запросов
        $sort_param = 'date_added';
        if (isset($params['save'])) {//сохранение новой записи
            add_new_task($params['description']);
        } elseif (isset($params['sort'])) {//выборка и сортировка
            switch ($params['sort_by']) {//выбираем поле сортировки
                case 'date_added':
                    $sort_param = 'date_added';
                    break;
                case 'is_done':
                    $sort_param = 'is_done';
                    break;
                case 'description':
                    $sort_param = 'description';
                    break;
                default:
                    $sort_param = 'date_added';
            }
        } else {//изменение, удаление
            if (isset($params['id'])) {
                $tmp_params = explode(';',$params['id']);
                $request_params['id'] = $tmp_params[0];//получаем id записи
                $tmp_params[1] = explode('=', $tmp_params[1])[1];//находим значение action
                switch ($tmp_params[1]) {
                    case 'done':
                        $request_params['is_done'] = 1;
                        edit_task($request_params);
                        break;
                    case 'edit':
                        //пока не реализовано;
                        break;
                    case 'delete':
                        delete_task($request_params['id']);
                        break;
                    default:
                        break;
                }
            }
        }
        $recset = get_tasks($sort_param);
        return $recset;
    }
    
    function do_request($request, $params)//выполняет запрос с параметрами
    {
        $db = NULL;
        $stmt = NULL;
        try {
            $db = get_connection();
            $stmt = $db->prepare($request);
            foreach ($params as $param) {
                $stmt->bindValue($param['fieldName'], $param['fieldValue']);
            }
            $stmt->execute();
            
            $db = NULL;
        } catch (Exception $error) {
            echo $error->getMessage();
        }
        return;
    }
    
    function add_new_task($description)
    {
        $request = "INSERT INTO tasks (description, is_done) VALUES (:description, 0)";
        $params = [
            [
                'fieldName' => ':description',
                'fieldValue' => $description
            ]
        ];
        do_request($request, $params);
        return;
    }
    
    function delete_task($id)
    {
        $request = "DELETE FROM tasks WHERE id=:id";
        $params = [
            [
                'fieldName' => ':id',
                'fieldValue' => $id
            ]
        ];
        do_request($request, $params);
        return;
        
    }
    
    function edit_task($input_params)
    {
        $request = "UPDATE tasks SET is_done=:is_done WHERE id=:id";
        $params = [
            [
                'fieldName' => ':is_done',
                'fieldValue' => $input_params['is_done']
            ],
            [
                'fieldName' => ':id',
                'fieldValue' => $input_params['id']
            ]
        ];
        do_request($request, $params);
        return;
    }

    function get_tasks($field)
    {
        //$request = "SELECT * FROM tasks ORDER BY :field";//так не работает, знак ? тоже, видимо, нужен знак =
        $request = "SELECT * FROM tasks ORDER BY $field";
        $db = NULL;
        $stmt = NULL;
        $result = [];
        try {
            $db = get_connection();
            $stmt = $db->prepare($request);
            if ($stmt->execute(array($field))) {
                $db = NULL;
                while ($row = $stmt->fetch()) {
                    $result[] = $row;
                }
            }
        } catch (Exception $error) {
            echo $error->getMessage();
            return get_empty_recordset();
        }
        if (empty($result)) {
            $result = get_empty_recordset();
        }
        return $result;
    }
    
