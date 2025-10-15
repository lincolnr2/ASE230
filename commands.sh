
#shows endpoint output message
curl -i -X GET http://localhost:8000

#Get all products
curl -i -X GET http://localhost:8000/products

#get product by ID
curl -i -X GET http://localhost:8000/products/6

#Add product
curl -i -X POST http://localhost:8000/products -H "Content-Type: application/json" -d '{"name": "pen", "price": 4, "stock": 50}'

#Delete product
curl -i -X DELETE localhost:8000/products -H "Content-Type: application/json" -d '{"id": 9, "token": "token"}'

#Put product
curl -i -X PUT localhost:8000/products -H "Content-Type: application/json" -d '{"id": 16, "name": "basket", "price": 25, "stock": 15}'


#Get all managers
curl -i -X GET http://localhost:8000/managers

#Get manager by ID
curl -i -X GET http://localhost:8000/managers/2

#Add manager
curl -i -X POST http://localhost:8000/managers -H "Content-Type: application/json" -d '{"name": "mike", "email": "mike@a.com", "permissions": 4, "token": "token"}'

#Delete Manager
curl -i -X DELETE localhost:8000/managers -H "Content-Type: application/json" -d '{"id": 2, "token": "token"}'

#Put manager
curl -i -X PUT localhost:8000/managers -H "Content-Type: application/json" -d '{"id": 3, "name": "Pat", "permissions": 1, "token": "token"}'

#Get all sales
curl -i -X GET http://localhost:8000/sales

#get sale by ID
curl -i -X GET http://localhost:8000/sales/1

#Add sale
curl -i -X POST http://localhost:8000/sales -H "Content-Type: application/json" -d '{"startdate": "12-12-12T12:00:00", "enddate": "12-12-26T12:00:00", "discount": 12.5}'

#Delete sale
curl -i -X DELETE localhost:8000/sales -H "Content-Type: application/json" -d '{"id": 4}'


#Sign in & recieve token
curl -i -X POST localhost:8000/login -H "Content-Type: application/json" -d '{"uname": "admin", "upass": "admin"}
