<?php
/**
 * @author      Dennis Rogers <dennis@drogers.net>
 * @address     www.drogers.net
 */

require_once(dirname(__FILE__) . "/app.php");

$bdo->run();

$json = array(
    'characters' => $bdo->getCharacters(),
    'user' => array(
        'id' => $bdo->getUser(),
        'max' => $bdo->getMax()
    )
);

echo json_encode($json);
