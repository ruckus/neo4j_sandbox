<?php

require 'db_connect.php';
require 'neo_connect.php';
// Create Users in Neo4j from database records
$users = users();
foreach($users AS $user) {
  create_user_node($user);
}

// Create friendships in Neo4j from database records
$friendships = friendships();
foreach($friendships AS $friendship) {
  $user = user_by_id($friendship['user_id']);
  $friend = user_by_id($friendship['friend_id']);
  create_neo_friendship($user, $friend);
}

// Create place nodes
$places = places();
foreach($places AS $place) {
  create_place_node($place);
}

$user_db = user_by_id(64);
$place_db = place_by_id(5);
create_neo_list_entry($user_db, $place_db);

$user_db = user_by_id(65);
$place_db = place_by_id(6);
create_neo_list_entry($user_db, $place_db);


function create_user_node($properties) {
  global $client, $user_name_index, $user_id_index;
  
  // only insert this node if we have not already seen it
  $index_entry = $user_id_index->findOne('dbid', $properties['id']);
  //print_r($index_entry);
  if($index_entry == NULL) {
    echo "Creating: " . $properties['name'] . "\n";
    $node = $client->makeNode();
    $node->setProperty('name', $properties['name'])->save();
    $node->setProperty('dbid', $properties['id'])->save();
    $user_name_index->add($node, 'name', $properties['name']);
    $user_id_index->add($node, 'dbid', $properties['id']);
  } else {
    echo "Skipping: " . $properties['name'] . "\n";
  }
}

function create_neo_friendship($user_db, $friend_db) {
  global $user_id_index;
  $user = $user_id_index->findOne('dbid', $user_db['id']);
  if($user !== NULL) {
    $friend = $user_id_index->findOne('dbid', $friend_db['id']);
    if($friend !== NULL) {
      echo "Friends: " . $user_db['name'] . " to " . $friend_db['name'] . "\n";
      $user->relateTo($friend, 'FRIENDS')->save();
      $friend->relateTo($user, 'FRIENDS')->save();
    }
  }
}

function create_place_node($record) {
  global $place_id_index, $client;
  $place_node = $place_id_index->findOne('dbid', $record['id']);
  /*
  if($place_node === NULL) {
    echo "Creating place index: " . $record['name'] . "\n";
    $place_index->add($place_node, 'dbid', $record['id']);
  }
  */
  if($place_node === NULL) {
    echo "Creating place: " . $record['name'] . "\n";
    $node = $client->makeNode();
    $node->setProperty('name', $record['name'])->save();
    $node->setProperty('dbid', $record['id'])->save();
    $place_id_index->add($node, 'dbid', $record['id']);
  }
}

function create_neo_list_entry($user_db, $place_db) {
  global $client, $place_id_index, $user_id_index;

  $user_node = $user_id_index->findOne('dbid', $user_db['id']);
  $place_node = $place_id_index->findOne('dbid', $place_db['id']);
  if($user_node !== NULL && $place_node !== NULL) {
    echo $user_db['name'] . " recommends " . $place_db['name'] . "\n";
    $user_node->relateTo($place_node, 'RECD')->save();
  } else {
    if($user_node === NULL) {
      echo "NULL user: " . $user_db['name'] . "\n";
    }
    if($place_node === NULL) {
      echo "NULL place: " . $place_db['name'] . "\n";
    }
  }
}


/*
// Delete data in neo
for($i = 0; $i < 400; $i++) {
  $node = $client->getNode($i);
  if($node) {
    echo $node->getProperty('name') . " - " . $node->getID() . "\n";
    $rels = $node->getRelationships();
    print_r($rels);
    foreach($rels as $rel) {
      $rel->delete();
    }
    $node->delete();
    try {
      $name_index->remove($node);
    } catch(Exception $e) {
      // silently swallow
    }
    //print_r($node);
  }
}

exit;
*/

?>