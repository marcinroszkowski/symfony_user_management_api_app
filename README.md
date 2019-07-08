# [Symfony 4.3] User management through REST API

Basic Symfony 4.3 application with functionality of registration and login extended with user management through REST API (input/output data as JSON).

Usage of API methods require API Token which is presented at index page after successful registration and login of a user (registration form).

# API response examples
Response examples for successful requests, however errors are handled either. 

## GET /api/users/1
### Output
#### HTTP Status 200
```json
{
  "username": "apiuser",
  "email": "apiuser@somedomain.com",
}
```

## POST /api/users
### Input
```json
{
  "username": "apiuser",
  "email": "apiuser@somedomain.com",
  "password": "apiuser"
}
```
### Output
#### HTTP Status 201
```json
{
  "message": "User created!",
  "apiToken": "1218752d83923161bb272c6d45040e8153d92b7bdca367bac3f5bd80da70d357"
}
```

## PUT /api/users/1
### Input
##### Could be either one or 3 parameters
```json
{
  "username": "updatedusername",
  "email": "updatedusername@somedomain.com",
  "password": "updatedusername"
}
```

### Output
#### HTTP Status 200
```json
{
  "message": "User updated!"
}
```

## DELETE /api/users/1
### Output
#### HTTP Status 200
```json
{
  "message": "User deleted!"
}
```
