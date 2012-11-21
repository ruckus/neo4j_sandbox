<?php

require("phar://neo4jphp.phar");

$client = new Everyman\Neo4j\Client('localhost', 7474);
$user_name_index = new Everyman\Neo4j\Index\NodeIndex($client, 'idx_users_names');
$user_name_index->save();
$user_id_index = new Everyman\Neo4j\Index\NodeIndex($client, 'idx_users_dbids');
$user_id_index->save();
$place_id_index = new Everyman\Neo4j\Index\NodeIndex($client, 'idx_places_dbis');
$place_id_index->save();

?>