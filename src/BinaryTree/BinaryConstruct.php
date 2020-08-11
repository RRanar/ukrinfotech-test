<?php
namespace BinaryManagment\BinaryTree;

/*

Цель файла
Реализация классов для работы с бинаром. Предварительно создать таблицу для
хранения ячеек бинара. Изначально в корне бинара нужно поставить ячейку, от которой
будет построение дальнейшего дерева.

Структура таблицы:
id int(11) - идентификатор ячейки
parent_id int(11) - идентификатор родителя
position int(11) - позиция ячейки относительно родителя (1 ли 2), то есть слева или справа от родителя
path varchar(12288) -  путь ячейки вида 1.3.8, где 8 это id текущей ячейки, а 3 и 1 - это родители ячейки снизу вверх.https://gist.github.com/codedokode/10539720#4-materialized-
path
level int(11) - уровень бинара, начиная от 1

Условие 1. Реализовать класс для построения бинара. Он должен принимать
parent_id и position для создания ячейки, остальные данные должен формировать
автоматически.
 */
class BinaryConstruct {
    protected $dbBinaryNodes; //Поле dbBinaryNodes будет использоваться для подключения к таблице и манипуляции с данными таблицы
    
    public function __construct() 
    {
        $this->initRoot(); //Предварительно создаем таблицу для хранения ячеек бинара и ставим в корне бинара ячейку от которой будет посторенно дерево.
    }
    public function createNode ($parent_id, $position){ //Метод для создания ячейки.Согласно условию принимает parent_id и position
        if($position === 2 || $position === 1){ //Делаем проверку не введена ли некоректная позиция .Согдасно условию 1 - левая ветка , 2 - правая ветка
            $p_data = $this->getNodeData($parent_id);  //Получаем данные родительской ячейки
            if(count($p_data)){ //проверка были ли получены данные или нет
                $new_data = ['id' => 2*$p_data['id'] + ($position -1 ),'parent_id' => $p_data['id'] + 0, 'position' => $position, 'path' => "$p_data[path].".(2*$p_data['id'] + ($position -1)), 'level' => $p_data['level'] + 1]; //формируем новую ячейку.id строим по формуле для бинарного дерева 2*n - если слева, 2*n+1 если справа.Path по условию. 
                if($this->addNode($new_data)){ //проверка была ли добавлена ячейка
                    return true;//если успех вернем true
                } 
            } 
        }
        return false; //соответственно если все условия не выполнятся возвращаем bool false
    }
    
    protected function openConnection(){ //метод для подключения к базе данных
        if(DB_NAME !== "" && DB_PASS !== "" && DB_USER !== ""){ //проводим проверку заполенны или нет поля имени БД, юзера для доступа в БД и пароля в config.php 
            $this->dbBinaryNodes = new \mysqli("", DB_USER, DB_PASS, DB_NAME);//создаем подключение к БД используя данные из config.php
            if(!$this->dbBinaryNodes->connect_errno) { //если не возникло ошибок вернем true
                return true;
            }
        }
        return false;
    }
    
    protected function closeConnection(){ //метод для закрытия соединения 
        $this->dbBinaryNodes->close(); 
    }

    protected function initRoot() //вспомагательный метод для создания базы данных и установки ячейи в корень
    {
        if($this->openConnection()){//открываем соединение
            if($this->dbBinaryNodes->query("CREATE TABLE BinaryNodes (  
                id INT(11),
                parent_id INT(11),
                position INT(11),
                path VARCHAR(12288),
                level INT(11),
                PRIMARY KEY (id)
            );")){//создаем таблицу с полями согласно условия задачи
                $this->closeConnection();//если всё ок закрываем соединение
                $root_node = array('id' => 1,'parent_id' => 0, 'position' => 0, 'path' => "1", 'level' => '1');//формируем ячейку корня
                $this->addNode($root_node);//добавляем корень 
            }
        }
    }

    protected function getNodeData($node_id) //вспомагательный метод для получения данных ячейки
    {
        if($this->openConnection()){ //если удалось подключится 
            if($res = $this->dbBinaryNodes->query("SELECT * FROM BinaryNodes WHERE id=$node_id")){ 
                $this->closeConnection(); //в случае успеха закрыли соединение и вернули ассоциативный массив с данными
                return $res->fetch_assoc();
            }
        }
        return [];
    }

    protected function addNode($node_data) //вспомагательный метод для добавления ячейки
    {
        if($this->openConnection()){//если удалось подключится 
            if($this->dbBinaryNodes->query("INSERT INTO BinaryNodes VALUES($node_data[id], $node_data[parent_id], $node_data[position],'".$node_data['path']."', $node_data[level] )")){ // если запрос удачный возвразаем bool true
                $this->closeConnection();
                return true;  
            }
        }
        return false;//в противном случае false
    }
}
?>