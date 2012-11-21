<?php

$conn = pg_connect('host=localhost dbname=neo4j_sandbox user=postgres');
if(!$conn) { die('Failed to connect to Postgres'); }

$result = pg_prepare($conn, "user_query", "SELECT * FROM users WHERE name = $1 LIMIT 1");
$result = pg_prepare($conn, "user_by_id", "SELECT * FROM users WHERE id = $1");
$result = pg_prepare($conn, "place_by_id", "SELECT * FROM places WHERE id = $1");
$result = pg_prepare($conn, "place_by_name", "SELECT * FROM places WHERE name = $1 LIMIT 1");

function user($name) {
  global $conn;
  $result = pg_execute($conn, "user_query", array($name));
  $user = array();
  while($row = pg_fetch_assoc($result)) {
    $user = $row;
  }
  pg_free_result($result);
  return($user);
}

function user_by_id($id) {
  global $conn;
  $result = pg_execute($conn, "user_by_id", array($id));
  $user = array();
  while($row = pg_fetch_assoc($result)) {
    $user = $row;
  }
  pg_free_result($result);
  return($user);
}

function users() {
  global $conn;
  $result = pg_query($conn, 'SELECT * FROM users');
  $users = array();
  while($row = pg_fetch_assoc($result)) {
    $users[] = $row;
  }
  pg_free_result($result);
  return($users);
}

function places() {
  global $conn;
  $result = pg_query($conn, 'SELECT * FROM places');
  $places = array();
  while($row = pg_fetch_assoc($result)) {
    $places[] = $row;
  }
  pg_free_result($result);
  return($places);
}

function place_by_id($id) {
  global $conn;
  $result = pg_execute($conn, "place_by_id", array($id));
  $place = array();
  while($row = pg_fetch_assoc($result)) {
    $place = $row;
  }
  pg_free_result($result);
  return($place);
}

function place_by_name($name) {
  global $conn;
  $result = pg_execute($conn, "place_by_name", array($name));
  $place = array();
  while($row = pg_fetch_assoc($result)) {
    $place = $row;
  }
  pg_free_result($result);
  return($place);
}

function friendships() {
  global $conn;
  $result = pg_query($conn, 'SELECT * FROM friendships');
  $friendships = array();
  while($row = pg_fetch_assoc($result)) {
    $friendships[] = $row;
  }
  pg_free_result($result);
  return($friendships);
}

function create_user($name) {
  global $conn;
  $user = user($name);
  if(empty($user)) {
    if(pg_query($conn, sprintf("INSERT INTO users (name) VALUES ('%s')", $name)) !== FALSE) {
      return(user($name));
    } else {
      throw new Exception("Failed to create_user $name");
    }
  }
}

function create_friendship($user, $friend) {
  global $conn;
  $insert = sprintf("INSERT INTO friendships (user_id,friend_id) VALUES (%d, %d)", 
    $user['id'], $friend['id']
  );
  return(pg_query($conn, $insert) !== FALSE);
}

?>