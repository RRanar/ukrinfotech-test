<?php
namespace BinaryManagment\BinaryTree;
/*
Условие 2. Реализовать отдельно класс для управления бинаром. Класс будет
автоматически заполнять бинар до 5 уровня, включительно, слева направо, сверху вниз.
Также класс должен дать возможность получить по id ячейки все нижестоящие и
вышестоящие ячейки.
*/
class BinaryManager extends BinaryConstruct{ //унаследуем вспомогательный функционал
   
    public function generateFiveLevels() //метод для генерации древа до level=5 
    {
        $mlevel = $this->getMaxLevel(); //на случай если древо предварительно было заполнено данными получим его текущую глубину
        if($mlevel >= 5){ //если глубина больше или равна 5 прекратим выполнение
            return true;
        }
        $mlevel_nodes = $this->getAllLevelNodes($mlevel); // получаем все ячейки на максимальном уровне
        if(count($mlevel_nodes)){ 
            for($i = 0;$i < count($mlevel_nodes);$i++){  //генерируем новые ячейки до 5-го уровня рекурчивно слева- направо сверху вниз
                if(!$this->createNode($mlevel_nodes[$i], 1)||!$this->createNode($mlevel_nodes[$i], 2)){
                    break;
                }
            }
            $this->generateFiveLevels();
        }
        return false;
    }

    protected function getMaxLevel() //вспомогательный метод для получения MAX(level)
    {
        if($this->openConnection()){
            if($res = $this->dbBinaryNodes->query("SELECT MAX(level) as mlevel FROM BinaryNodes")){
                $data = $res->fetch_assoc();
                $this->closeConnection();
                return (int)$data['mlevel'];
            } 
        }
        return 0;
    }

    protected function getAllLevelNodes($level) //вспомогательный метод для получения id ячеек на выбраном level
    {
        if($this->openConnection()){
            if($res = $this->dbBinaryNodes->query("SELECT * FROM BinaryNodes WHERE level=$level")){
                $nodes_id = [];
                while($data = $res->fetch_assoc()){
                    array_push($nodes_id,$data['id']);
                }
                $this->closeConnection();
                return $nodes_id;
            }
        }
        return [];
    }

    protected function getParentsNodes($node_id) //вспомагательный метод для получения родительски яячеек - ячеек сверху
    {
        $node_data = $this->getNodeData($node_id);
        if(count($node_data)){
            $node_path = $node_data['path'];//получаем путь
            $parents = preg_split("/\./",$node_path);
            array_pop($parents);
            $nodes=[];
            foreach($parents as $value){ // добавляем всех родителей с path
                array_push($nodes, $this->getNodeData($value));
            }
            return $nodes;
        }
        return [];
    }

    public function getAllUpDownNodes($node_id) //метод для получения ячеек сверху и снизу от заданой
    {
        $result = [];
        array_push($result,$this->getParentsNodes($node_id), $this->getChildrenNodes($node_id)); //берём всех родителей и берём все ячейи снизу
        return $result; //вернем массив ассоциативных массивов с данными ячеек для работы с ними
    }

    protected function getChildrenNodes($node_id) //вспомогательный метод для получения ячеек снизу
    {
        $node_data = $this->getNodeData($node_id); //получаем данные ячейки
        if(count($node_data)){
            $node_path = $node_data['path'].".%"; 
            if($this->openConnection()){
                if($res = $this->dbBinaryNodes->query("SELECT * FROM BinaryNodes WHERE path LIKE '$node_path'")){ // выбираем всех нижестоящих детей по path
                    $nodes = array();
                    while($data = $res->fetch_assoc()){
                        array_push($nodes,$data);
                    }
                    $this->closeConnection();
                    return $nodes; //возвращаем результат в ввиде масива ассоциативных массивов с данными ячеек            
                }
            }
        } 
        return [];//если ошибка пустой массив
    }
}
?>