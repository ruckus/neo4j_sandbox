<?php

require 'db_connect.php';
require 'neo_connect.php';

// Cypher: find all friends-of-friends (maxdepth=2) that have recommended either of these two places
$place_id = 5;
$cypher = "START cody=node:idx_users_names(name = 'cody')"
  . " MATCH p1=(cody)-[r:FRIENDS*1..2]->fof-[:RECD]->place"
  . " WHERE place.dbid IN ['5', '6']"
  . " RETURN DISTINCT cody, fof.name, place.name, LENGTH(p1)";

$query = new Everyman\Neo4j\Cypher\Query($client, $cypher);
$result = $query->getResultSet();

foreach($result as $row) {
  echo "FoF: " . $row['fof.name'] . "\n";
  echo "Recommends: " . $row['place.name'] . "\n";
  echo "Distance: " . $row['LENGTH(p1)'] . "\n";
}
echo "\n--------------- Traversals: Codys Friends & Friends of Friends (maxdepth=2) -----------------\n";

// Traversals
$traversal = new Everyman\Neo4j\Traversal($client);
$traversal->addRelationship('FRIENDS', Everyman\Neo4j\Relationship::DirectionOut)
    ->setPruneEvaluator(Everyman\Neo4j\Traversal::PruneNone)
    ->setReturnFilter(Everyman\Neo4j\Traversal::ReturnAllButStart)
    ->setOrder(Everyman\Neo4j\Traversal::OrderDepthFirst)
    ->setMaxDepth(2);

$cody = $user_name_index->findOne('name', 'cody');
if($cody !== NULL) {
  // Chose a return type
  $type = Everyman\Neo4j\Traversal::ReturnTypeNode;
  //$type = Everyman\Neo4j\Traversal::ReturnTypePath;
  $nodes = $traversal->getResults($cody, $type);
  echo count($nodes) . " friends\n";

  if($type == Everyman\Neo4j\Traversal::ReturnTypePath) {
    foreach($nodes as $node) {
      $id = $node->getEndNode()->getID();
      $name = $node->getEndNode()->getProperty('name');
      echo "At depth " . $node->getLength() . " : " . $id . " => " . $name;
      echo "\n";
    }
  }

  if($type == Everyman\Neo4j\Traversal::ReturnTypeNode) {
    foreach($nodes as $node) {
      echo "\t" . $node->getProperty('name') . ' (id=' . $node->getID() . ')';
      echo "\n";
    }
  }
}

?>