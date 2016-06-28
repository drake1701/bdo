<?php
/**
 * @author      Dennis Rogers <dennis@drogers.net>
 * @address     www.drogers.net
 */
 
class BDO_App
{
    public $db;
    protected $user;
    protected $max;
    public $baseDir = '/var/www/bdo.drogers.net/';
    
    const ONLINE = 1;
    const OFFLINE = 2;
    const BED = 3;
    const CASHBED = 4;
    
    function __construct() {
        
        session_start();
        
        $this->db = new PDO('sqlite:'.$this->baseDir.'bdo.sqlite');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function run() {
        $this->getUser();

        if(isset($_POST) && !empty($_POST)) {
            $this->update($_POST);
            unset($_POST);
        }
    }
    
    public function loadUser($post) {
        if(!isset($post['key']) || empty($post['key'])) return false;
        
        if(isset($post['new']) && $post['new'] == 1) {
            $insert = $this->db->prepare('INSERT INTO `user` (`key`) VALUES (:key);');
            $insert->bindParam(':key', $post['key']);
            $insert->execute();
        }
        
        $user = $this->db->prepare('SELECT * FROM `user` WHERE `key` = :key;');
        $user->bindParam(':key', $post['key']);
        $user->execute();
        
        $user = $user->fetch();
        if(isset($user['id']) && !empty($user['id'])) {
            $_SESSION['user'] = $user['id'];
            $this->user = $user['id'];
        }
    }
    
    public function getUser()
    {
        global $baseDir;
        
        if(!$this->user) {
            if(is_array($_POST) && isset($_POST['key'])) {
                $this->loadUser($_POST);
            } elseif(!isset($_SESSION['user']) || empty($_SESSION['user'])) {
                require($baseDir.'includes/login.phtml');
                die();
            } else {
                $this->user = $_SESSION['user'];    
            }
        }   
        return $this->user;
    }
    
    public function getMax()
    {
        if(!$this->max) {
            $selectMax = $this->db->prepare('SELECT `max_energy` FROM `user` WHERE `id` = :id;');
            $selectMax->bindParam(':id', $this->getUser());
            $selectMax->execute();
            
            $this->max = $selectMax->fetchObject()->max_energy;
        }
        return $this->max;
    }
    
    public function getCharacters()
    {        
        $characterSelect = $this->db->prepare('SELECT * FROM `character` WHERE `user_id` = :user_id ORDER BY `energy` DESC;');
        $characterSelect->bindParam(':user_id', $this->getUser());
        $characterSelect->execute();
        
        $characters = array();
        while($row = $characterSelect->fetchObject()) {
            $remaining = $this->getMax() - $row->energy;
            
            switch($row->state) {
                case BDO_App::ONLINE:
                    $hours = $remaining / 20;
                    break;
                case BDO_App::OFFLINE:
                    $hours = $remaining / 2;
                    break;
                case BDO_App::BED:
                    $hours = $remaining / 40;
                    break;
                case BDO_App::CASHBED:
                    $hours = $remaining / 60;
                    break;
            }
            
            $row->time = "$hours hours";
            
            $characters[] = $row;
        }
            
        return $characters;
    }
    
    protected function update($post)
    {
        if(isset($post['new']) && !empty($post['new'])) {
            $insert = $this->db->prepare('INSERT INTO `character` (`name`, `user_id`) VALUES (:name, :user_id);');
            $insert->bindParam(':name', $post['new']);
            $insert->bindParam(':user_id', $this->getUser());
            $insert->execute();
        }       
        
        if(isset($post['max']) && !empty($post['max'])) {
            $update = $this->db->prepare('UPDATE `user` set `max_energy` = :max WHERE `id` = :id;');
            $update->bindParam(':max', $post['max']);
            $update->bindParam(':id', $this->getUser());
            $update->execute();
        }
        
        $currentData = $this->getCharacters();

        if(count($post['state'])) {
            $reset = $this->db->prepare('UPDATE `character` set `state` = ' . self::OFFLINE . ' WHERE `user_id` = :user_id;');
            $reset->bindParam(':user_id', $this->getUser());
            $reset->execute();
            
            foreach($post['state'] as $id => $state) {
                $update = $this->db->prepare('UPDATE `character` set `state` = :state WHERE `id` = :id;');
                $update->bindParam(':state', $state);
                $update->bindParam(':id', $id);
                $update->execute();
            }            
        }

        if(count($post['energy'])) {            
            foreach($post['energy'] as $id => $energy) {
                if($energy === '') continue;
                $update = $this->db->prepare('UPDATE `character` set `energy` = :energy WHERE `id` = :id;');
                $update->bindParam(':energy', $post['energy'][$id]);
                $update->bindParam(':id', $id);
                $update->execute();
            }            
        }
        
        if(count($post['name'])) {            
            foreach($post['name'] as $id => $name) {
                $name = trim($name);
                
                if($name == '') {
                    $update = $this->db->prepare('DELETE FROM `character` WHERE `id` = :id;');
                    $update->bindParam(':id', $id);
                    $update->execute();
                } else {
                    $update = $this->db->prepare('UPDATE `character` set `name` = :name WHERE `id` = :id;');
                    $update->bindParam(':name', $post['name'][$id]);
                    $update->bindParam(':id', $id);
                    $update->execute();
                }
            }            
        } 

    }
        
    public static function log($message)
    {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $file = 'system.log';

        try {
            $logDir  = dirname(__FILE__) . 'var/';
            $logFile = $logDir . '/' . $file;

            if (!is_dir($logDir)) {
                mkdir($logDir);
                chmod($logDir, 0750);
            }

            if (!file_exists($logFile)) {
                file_put_contents($logFile, '');
                chmod($logFile, 0640);
            }
            
            $log = fopen($logFile, 'a');
            fputs($log, date('c') . ': ' . $message . "\n");
            fclose($log);
            
        }
        catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
}

$bdo = new BDO_App();

