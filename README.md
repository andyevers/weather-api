# Weather API
Accepts requests to endpoint "/api/weather/office/forecast" and communicates with weather.gov API to get forecast data. 


## API Installation
1. Create a directory on your server where you want to host the API. (ex: `Applications/MAMP/htdocs/weather-api`).
2. Install the project files. Open a terminal in your empty directory and run the command: 
```console
git clone https://github.com/andyevers/weather-api .
```
3. Create a new database and run the following SQL query on your database:
```SQL
CREATE TABLE api_tokens (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    Token varchar(64) NOT NULL UNIQUE,
    UsageCount int NOT NULL DEFAULT 0,
    LastUsedOn timestamp
);
```
4. Add the demo rows by running the following SQL query on your database:
```SQL
INSERT INTO api_tokens(Token) 
VALUES 
("QkgAVGXuebE9beJEV6iaMKRWf4eDAtALwi9FibuXvR37HYqEJuQKmVdv9eUEyx88"), 
("3o2fQgpAfxmQhPDsvhDThhyDMZZ7bRh7VcUGAn24UYJWnjVFDtnfZk77Go6NxB62");
```
5. Open the file `/model/Database.php` and change these variables to your database name, user, password, and host respectively:
```PHP
private $db_name = 'my_database';
private $db_user = 'root';
private $db_pass = 'root';
private $db_host = 'localhost';
```


## Sending API Requests
Send a POST request to the URL of your API installation: `[API_LOCATION]/api/weather/office/forecast`.

You must include `X-Api-Token: [TOKEN_ID]` in your header or you will get a 401 unauthorized missing token error.

If the API token provided does not exist in the database, you will also get a 401 unauthorized error.

__Send request in terminal using curl__:
```console
curl -H "X-Api-Token: QkgAVGXuebE9beJEV6iaMKRWf4eDAtALwi9FibuXvR37HYqEJuQKmVdv9eUEyx88" [API_LOCATION]/api/weather/office/forecast --head
```
executing the above command in your terminal will display the response data followed by the headers from your request.


## Handle 404 Not Found Error 
If you are getting a 404 error when sending a request to your API, it may be because the .htaccess file is not working. This is responsible for routing all requests to the index.php file. 

Here are a couple possible solutions for this problem:

__Solution 1__ <br>
Try openning your httpd.conf file and ensure you have the following settings:
```txt
<Directory />
    Options Indexes FollowSymLinks
    AllowOverride All
</Directory>

AccessFileName .htaccess
```
__Solution 2__ <br>
(Workaround) Include `index.php` in your POST request: `[API_LOCATION]/index.php/api/weather/office/forecast`# weather-api
