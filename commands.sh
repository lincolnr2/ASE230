
#shows endpoint output message
curl -i -X GET http://localhost:8000

#Get all products
curl -i -X GET http://localhost:8000/products

#get product by ID
curl -i -X GET http://localhost:8000/products/6

#Add product
curl -i -X POST http://localhost:8000/products -H "Content-Type: application/json" -d '{"name": "pen", "price": 4, "stock": 50}'

#More to be implemented - put & delete return 501

