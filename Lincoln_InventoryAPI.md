---
marp: true
---

# Inventory API system
Robert Lincoln
Lincolnr2@nku.edu

---

# Required software

Can be downlaoded through command line or individual sites


- Brew (mac)
- PHP
- MySQL
- IDE (VSCode)



---

# PHP

PHP is a server side scripting language. In this project, we use PHP to code the interactions between front and back end.

Front end interactions are done through HTML and Javascript, or through command line with the use of curl. 

In this project, we directly run the php, but in the future we will implement better tactics for the server system. 

---

# PHP Syntax

PHP code is encased within <?php  ...  ?>

Variables can be declared with $ such as $variable = 7;

PHP variables are initially dynamically typed. Strict typing can be used by adding declare(strict_types=1) after <?php

all statements are ended with semicolons;


To start the php server, move into the server directory and run php -S localhost:8000

This will start the server, allowing access to commands or webpage at http://localhost:8000

---

# MySQL database

SQL is a database management software that allows the storage, update, and use of information. 

In this project, we use it to store information about products, managers, sales, users, and security tokens. 

In SQL, We can use CRUD operations to modify, view, or delete the data in the database. 

CRUD stands for Create, Read, Update, and Delete and basic SQL structure is

insert into table (columns) values (item values)
select value, value2 from table where (condition)
update table set column = value where id = value
delete from table where id = value

---
# PDOs

To connect PHP adn SQL, we will use a PDO (PHP data object). This allows us to connect to the database and make crud requests. 

We use PDO to interact with sql because the commands are simple and repeatable, and the PDO is secure, not allowing sql injection to occur. 

Basic pdo requests consist of setting a sql command string, preparing the statement, executing the request, and fetching the results. 

---

# Rest APIs

## What are they?

Rest stands for Representational state transfer, and it allows for stateless communication between server and users. 

This means that data is not stored for any HTTP requests, rather all needed information is sent and recieved within the http requests. 

HTTP includes a method for each element of CRUD-

Create - POST
Read - GET
Update - PUT
Delete - DELETE

---

# HTTP Request and response
APIs use HTTP requests to talk to the server.

Each of these requests includes a method, headers, and data in the form of JSON

Responses from the server include headers and data, as well as a http response code. 
Some exaple response codes include - 
1xx - working
2xx - OK
3xx - redirect
4xx - request issue
5xx - server issue

Most importantly: 200(success), 201(created), 401(unauthorized), 403(forbidden), 404(not found) 
  



---


# How our API works

When an http request is made to the server, it is sent to index.php. Once it is recieved, index.php decides what resource it is targeting, and what method it is trying to use using multiple switch case statements.

Each endpoint (resource and method) calls a function from handlers.php, containing all the PDO functions and JSON outputs. 

Once the function is complete, it sends its output JSON data as well as an http response code back to the user. 


--- 

# Security

Multiple endpoints are secured within our API, including any that modify managers or deleting products. 

These are secured using bearer token authorization. This means each authorized user has their own secret token to access blocked endpoints.

To recieve this token, users must sign in with a valid username and password, and recieve thier token. 

The users submit thier token with request data if they are accessing a secure endpoint and auth.php will check if the token is valid and run the endpoint funciton. 

--- 

# Password Security

Valid username and passwords are hashed and stored in the database. This means they are turned to a long string of unintelligible characters, rather than stored as plain text.

This allows the passwords to be much more secure, as they must match exactly for the hashed string to match. Even a small change in the plain text password creates an entirely new hashed password. 

The passwords are compared with a password_verify() method, as each hashed password is different.

---

# Accessing the API using curl

The API can be accessed with curl commands from a CLI terminal

Basic command structure is:
 curl -X METHOD url -H "Content-Type: application/json" -d '{""}'

 -X : method 
 -H : Headers
 -d : body data
 -i : include response headers

Example: Show all products - 
curl -i -X GET localhost:8000/products 

--- 

# More curl examples

login to recieve token - 
curl -X POST localhost:8000/login -H "Content-Type: application/json" -d '{"uname": "admin", "upass": "admin"}'

Response:
{
    token: [Custom user token]
}

Delete product id 15- 
curl -i -X DELETE localhost:8000/product/15 -H "Content-Type: application/json" -d '{"token": "admin"}'

---

# Access using HTML and Javascript
To access via html, run the server and go to http://localhost:8000/index.html

This page has all the seperate endpoints on it, as well as a sign in option. 

For each endpoint, fill out the form with rescpective information and click submit to recieve data, formatted into a table.

To access the protected APIs, enter login information to the username and password fields and submit to recieve a token. 

While you have a token active, it will automatically pass to endpoints that require it, as opposed to having to manually input it with curl.


---

# Summary

Our Inventory API is used to view and edit products, managers, and sales. 

The API is built using PHP, and connects HTML & JS pages to an sql database containing the information.

Being a REST API, it does not store any data itself, rather all data for the server is stored in the database. 

The API allows us to easily manage the information, as well as keeping it safe. 

Important endpoints, such as creating new managers are secured using bearer tokens, which require username and password to access

---

# Next Steps

In project 2, we will be expanding our API server to be publicly accessible, as it is currently only accessible on local systems. 

We will implement a few new features - 
laravel -  enhancing the php structure and allowing static file implementation

ORM - Enhancing database interactions & SQL

Docker - Containers and deployment of full site 



