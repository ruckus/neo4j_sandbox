Collection of PHP scripts to play with Neo4j graph database via its REST API.

Uses the excellent PHP Neo4j API client: https://github.com/jadell/neo4jphp

## Getting Started

Create the Postgres database:

```
psql> create database neo4j_sandbox;
```

Import the sample structure

```
$ psql -U postgres neo4j_sandbox < db.sql
```

Run Neo4j in another terminal:

```bash
$ cd /path/to/your/neo4j/download
$ sudo bin/neo4j start
Starting Neo4j Server...process [12743]... waiting for server to be ready...... OK.
Go to http://localhost:7474/webadmin/ for administration interface.
```

Now we want to populate Neo4j with data from the database. This PHP script will connect to Postgres and create the nodes and friendships.

The Postgres connection credentials are set as:

`host=localhost dbname=neo4j_sandbox user=postgres`

If these are not valid for your machine update them in `db_connect.php`.

```bash
$ php populate_neo.php
```
If all goes well you should see output kind of like:

```
Friends: cody to gary
Friends: cody to chris
Friends: cody to josh
Friends: cody to gerald
Friends: cody to carol
Friends: cody to rebecca
Friends: gary to cody
Friends: gary to chris
Friends: gary to melissa
Friends: gary to suzanne
Friends: chris to cody
Friends: chris to gary
Friends: melissa to tom
Friends: melissa to hunter
melissa recommends 25 Lusk
suzanne recommends Zuni Cafe
```

Finally we can run the query code which in turn runs the Cypher query. The place IDs (5,6) are hard-coded into the Cypher query
but would of course need to be dropped into dynamically.

The query asks: "Given a start node of Cody, show me all friends and friends-of-friends (so maxdepth=2) who have recommended
places 5 (25 Lusk) and 6 (Zuni Cafe).

The output just shows the name of the recommender and their distance. A distance of 2 means a direct friendship where 3+ means friend-of-friend.

```
$ php intro.php
```

Output:

```
FoF: melissa
Recommends: 25 Lusk
Distance: 3
FoF: suzanne
Recommends: Zuni Cafe
Distance: 3

--------------- Traversals: Codys Friends & Friends of Friends (maxdepth=2) -----------------
6 friends
	chris (id=3)
	gary (id=2)
	rebecca (id=7)
	carol (id=6)
	gerald (id=5)
	josh (id=4)
```