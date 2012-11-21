<?php

require 'db_connect.php';

$users = array(
  'cody' => array('gary', 'chris', 'josh', 'gerald', 'carol', 'rebecca'),
  'gary' => array('cody', 'chris', 'melissa', 'suzanne'),
  'chris' => array('cody', 'gary'),
  'melissa' => array('tom', 'hunter')
);

pg_query($conn, 'DELETE FROM users');
pg_query($conn, 'DELETE FROM friendships');
pg_query($conn, 'DELETE FROM places');
pg_query($conn, 'DELETE FROM lists');

/* Create Users */
foreach($users as $key => $value) {
  create_user($key);
  foreach($value as $user) {
    create_user($user);
  }
}

/* Create Friendships */
foreach($users as $key => $value) {
  $user = user($key);
  foreach($value as $record) {
    $friend = user($record);
    create_friendship($user, $friend);
  }
}

pg_query($conn, sprintf("INSERT INTO places (name) VALUES ('%s')", "25 Lusk"));
$place = place_by_name('25 Lusk');
$user = user('melissa');

pg_query($conn, sprintf("INSERT INTO lists (user_id, place_id) VALUES (%d, %d)",
  $user['id'], $place['id']
));

print_r($user);
echo "\n-------------\n";
print_r($place);

?>