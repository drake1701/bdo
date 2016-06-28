<?php
/**
 * @author      Dennis Rogers <dennis@drogers.net>
 * @address     www.drogers.net
 */

require_once(dirname(__FILE__).'/app.php');

$characterSelect = $bdo->db->prepare('SELECT c.*, u.max_energy FROM `character` c INNER JOIN `user` u ON c.`user_id` = u.`id`;');
$characterSelect->execute();

while($character = $characterSelect->fetchObject()) {
    
    $energy = (Int)$character->energy;
    
    if($energy == $character->max_energy) continue;
    
    switch($character->state) {
        case BDO_App::ONLINE:
            $energy += 1;
            break;
        case BDO_App::OFFLINE:
            BDO_App::log(date('i'));
            if(date('i') == 30 || date('i') == 0)
                $energy += 1;
            break;
        case BDO_App::BED:
            $energy += 2;
            break;
        case BDO_App::CASHBED:
            $energy += 3;
            break;
    }
    
    if($energy != $character->energy) {
        $update = $bdo->db->prepare('UPDATE `character` SET `energy` = :energy WHERE `id` = :id;');
        $update->bindParam(':energy', $energy);
        $update->bindParam(':id', $character->id);
        $update->execute();
    }
    
}
