# MindCleaner API

## Laravel Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

##Authentification

### function: get_access_token

method: POST    
parameters:     
(string) email, (string) password   
return: (string) token

request example:    
http://localhost:8000/api/v1/get_access_token  
form-data:      
    email:      "example@example.com"   
    password:    "password"     

successfull responce example:   
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak"
}

error responce example:     
{   
  "error": "invalid_credentials"    
}