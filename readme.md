# MindCleaner API
### Запросы к методам делаем с префиксом api/v1 
**v1** - номер версии

### Laravel Documentation

Documentation for the Laravel framework can be found on the [Laravel website](http://laravel.com/docs).

##Authentification
### Workflow:   
1. get_access_token - POST запросом с парой email+пароль получаем acces_token и сохраняем его на устройстве. Время жизни acces_token'а - 60 минут
2. делаем запросы к защищенным зонам api, access_token передаем либо в качестве параметра в строке GET-запроса, либо в Заголовке Authorization в виде "Bearer 'access_token'"
3. если access_token валидный, но expired, на клиенте можно вызвать функцию refresh_token и получить в ответ новый access_token (таким образом, если делать это автоматически по получении ответа expired, юзеру при регулярном использовании приложения месяцами можно не вводить свой пароль)
4. expired acess_token может быть refreshed в течение 2 недель с момента выдачи access_token'а
5. при вызове метода logout(), переданный в этом запросе access_token будет перемещен в blacklist. access_token'ы из черного списка обновлению не подлежит.
6. могут быть выданы несколько access_token'ов для одного юзера (например, для нескольких устройств). Каждый acess_token протухает и refresh'ится самостоятельно.

### Possible token errors
{
  "error": "token_invalid"
}

{
  "error": "token_expired"
}

{
  "error": "token_not_provided"
}

{
  "error": "token_absent"
}

### function: get_access_token

**method:** POST    
**parameters:**     
(string) email, - required|email|max:255    
(string) password - required       
**return:** (string) token

**request example:**    
http://localhost:8000/api/v1/get_access_token  
**form-data:**      
    email:      "example@example.com"   
    password:    "password"     

**successfull responce example:**   
{   
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak"  
}

**error responce example:**     
{
  "error": "invalid_credentials"
}   
status: 401 Unauthorized    

{
  "error": "could_not_create_token"
}   
status: 500 Internal Server Error   

{   
   "error":{    
        "password":["The password field is required."]
    }   
}   
status: 422 Unprocessable Entity    

### function: refresh_token

**method:** GET    
**parameters:** no parameters
  
**return:** (string) token

**request example:**    
http://localhost:8000/api/v1/refresh_token?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  
   
**successfull responce example:**   
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL3YxXC9yZWZyZXNoX3Rva2VuIiwiaWF0IjoxNDgzMjkwOTYzLCJleHAiOjE0ODMyOTcxOTMsIm5iZiI6MTQ4MzI5NjU5MywianRpIjoiYzVkY2E1NGFlNGFiYWMzNjRlNmQ5M2U5Yjg1NTcwYjQifQ.u4_qWGcoo_c08zxRhAXONu_McEAq3HyEPL4ohBy-JSo"
}

**error responce example:**     
{
  "error": "token_invalid"
}   
status: 400 Bad Request

{
  "error": "token_absent"
}   
status: 400 Bad Request

### function: logout_remote

**method:** GET    
**parameters:**     no parameters
**return:** (string) result

**request example:**    
http://localhost:8000/api/v1/logout?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  

**successfull responce example:**   
{   
  "result": "success"   
}

**error responce example:**     
{
  "error": "token_invalid"
}   
status: 400 Bad Request

{
  "error": "token_absent"
}   
status: 400 Bad Request

### function: register_remote

**method:** POST    
**parameters:**     
    (string) email, - required|email|max:255|unique:users   
    (string) password, - required|min:6|confirmed   
    (string) password_confirmation,     
    (string) fio, - required|max:255    
    (string) sex - required|in:"f","m"  
    (string) phone - numeric|max:32
  
**return:** (string) token  

**request example:**    
http://localhost:8000/api/v1/logout?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  
**form-data:**      
    email:      "example@example.com"   
    password:    "123456"   
    password_confirmation: "123456" 
    "fio":      "fio",  
    "sex":      "f",    
    "phone":    "79161234567"   
      
**successfull responce example:**   
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL3YxXC9yZWZyZXNoX3Rva2VuIiwiaWF0IjoxNDgzMjkwOTYzLCJleHAiOjE0ODMyOTcxOTMsIm5iZiI6MTQ4MzI5NjU5MywianRpIjoiYzVkY2E1NGFlNGFiYWMzNjRlNmQ5M2U5Yjg1NTcwYjQifQ.u4_qWGcoo_c08zxRhAXONu_McEAq3HyEPL4ohBy-JSo"
}   

**error responce example:**     
{   
    "error":{   
        "fio":["The fio field is required."],   
        "password":["The password confirmation does not match."]    
        }   
}   
status: 422 Unprocessable Entity

