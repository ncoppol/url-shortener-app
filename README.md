# URL Shortener App

## Installation and Setup
If you have docker and docker-compose installed, run `docker-compose up` while in the root project directory. Once complete the site should be available at `localhost:8080`. If you are currently using port 8080 or 13306, then you may want to modify the docker-compose.yml file before running.

If you want to install this with your own running web and mysql server:
* ensure you have all the requirements filled for running CodeIgniter4
* https://codeigniter4.github.io/userguide/intro/requirements.html
* install composer and run `composer install` while in the root project directory. 
* enter db credentials in `app/Config/Database.php` in the $default array. 
* run `php spark migrate` while in the root project directory to generate the table in the database. 

## Challenges
Took some time to figure out how to guarantee a unique short URL and avoid a collision until I realized the db insertion was guaranteed a unique id. Also took some time getting used to docker and codeigniter since those tools were new to me. Reading through the docs and going through their tutorials helped.

## Design Decisions
### Codeigniter
* this got rid of a lot of heavy lifting since db query building, routing, and api responses were built-in
### Short URL generation
* when short url is requested, we will insert a row into the db table
* we will retrieve that id and use it to generate the short url
* we convert the id to base62 to get the short url
* we will convert the short url back to the base10 to find the id and the proper full url to redirect to
* the shortest url's will be the first 62 (one character), and then increase each time we hit the power (62^x)
### Table structure
* one table called urls
* set id to int , which should allow over 4 billion unique short url's. If we need more we can change it to BIGINT.
* set full url to max varchar(10000), possible users would input more but most web servers would not process it. Apache and Nginx default max URI is around 8,000
* put clicks and nsfw flag in same table, select queries shouldn't lock up on updates to rows so don't see any harm in keeping it in same place for now.
### Web client 
* sends POST requests via the api created in Part1
* added Bootstrap just to make things a little nicer

## Future Improvements
* display more detailed error messages when url is invalid
* add expirations to short url's so we can free up and reuse id's
* clean up formatting on the web client
