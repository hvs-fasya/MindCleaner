# MindCleaner API
### Запросы к методам делаем с префиксом api/v1 
**v1** - номер версии

### Laravel Documentation

Documentation for the Laravel framework can be found on the [Laravel website](http://laravel.com/docs).

### Methods list

### Authentification
**_get_access_token_**    
**_refresh_token_**   
**_logout_remote_**   
**_register_remote_**     
**_update_user_remote_**  
    
### Event Types
**_get_event_types_remote_**    
**_add_event_type_remote_**     
**_destroy_event_type_remote_**   
**_update_event_type_remote_**

## Authentification

### Workflow:   
1. get_access_token - POST запросом с парой email+пароль получаем acces_token и сохраняем его на устройстве. Время жизни acces_token'а - 60 минут
2. делаем запросы к защищенным зонам api, access_token передаем либо в качестве параметра в строке GET-запроса, либо в Заголовке Authorization в виде "Bearer 'access_token'"
3. если access_token валидный, но expired, на клиенте можно вызвать функцию refresh_token и получить в ответ новый access_token (таким образом, если делать это автоматически по получении ответа expired, юзеру при регулярном использовании приложения месяцами можно не вводить свой пароль)
4. expired acess_token может быть refreshed в течение 2 недель с момента выдачи access_token'а
5. при вызове метода logout(), переданный в этом запросе access_token будет перемещен в blacklist. access_token'ы из черного списка обновлению не подлежит.
6. могут быть выданы несколько access_token'ов для одного юзера (например, для нескольких устройств). Каждый acess_token протухает и refresh'ится самостоятельно.

### todo: 
resetPassword functionality     
mail notifiers for register and so on events    
'passport' for application - not to allow somebody else register user   
delete_user_remote  

### Possible token errors
{"error": "token_invalid"}

{"error": "token_expired"}

{"error": "token_not_provided"}

{"error": "token_absent"}

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
{"result": "success"}   

**error responce example:**     
{"error": "token_invalid"}   
status: 400 Bad Request

{"error": "token_absent"}   
status: 400 Bad Request

### function: register_remote

**method:** POST    
**parameters:**     
    (string) email, - required|email|max:255|unique:users   
    (string) password, - required|min:6|confirmed   
    (string) password_confirmation,     
    (string) name, - required|max:255    
    (string) sex - required|in:"f","m"  
    (string) phone - numeric|max:32
  
**return:** (string) token  

**request example:**    
http://localhost:8000/api/v1/logout?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  
**form-data:**      
    email:      "example@example.com"   
    password:    "123456"   
    password_confirmation: "123456" 
    "name":      "name",  
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

## function: update_user_remote
(access_token обновлять не нужно)     

**method:** POST    
**parameters:**     
    (string) email, - email|max:255|unique:users->ignore(user_id)  
    (string) password, - min:6|confirmed   
    (string) password_confirmation,     
    (string) name, - max:255    
    (string) sex - in:"f","m"  
    (string) phone - numeric|max:32
  
**return:** (string) token  

**request example:**    
http://localhost:8000/api/v1/logout?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  
**form-data:**      
    email:      "example@example.com"   
    password:    "123456"   
    password_confirmation: "123456" 
    "name":      "name",  
    "sex":      "f",    
    "phone":    "79161234567"   
      
**successfull responce example:**   
{"result":"success"}    

**error responce example:**     
{   
    "error":{      
        "password":["The password confirmation does not match."]    
        }   
}   
status: 422 Unprocessable Entity    

{"error":"could_not_update_user"}     
status: 500 Internal Server Error

## Event Types

## get_event_types_remote

**method:** GET    
*parameters:**     no parameters    
**return:** (array) event_types

**request example:**    
http://localhost:8000/api/v1/et_event_types_remote?query=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2dldF9hY2Nlc3NfdG9rZW4iLCJpYXQiOjE0ODMyOTA5NjMsImV4cCI6MTQ4MzI5MTU2MywibmJmIjoxNDgzMjkwOTYzLCJqdGkiOiJhYjcyMjdjZjFlNzQ2ZGYxOTM2NmUxMDM5NWE3YWExYyJ9.SkC9MBvp_iq7ZosW9tgFSAqgN10c8xjrIJ-1pTD6zak  

**successfull responce example:**   
{   
  "event_types": [  
    {   
      "id": 1,    - значение id на сервере
      "description": "Негативные и навязчивые мысли",   
      "common": true,    - аттрибут является ли этот вид события общим   
      "updated_at": null    
    },  
    {   
      "id": 2,    
      "description": "Навязчивые и негативные воспоминания",    
      "common": true,    
      "updated_at": null    
    },  
    {   
      "id": 3,    
      "description": "Неправильные поступки",   
      "common": true,   
      "updated_at": null    
    },  
    {   
      "id": 4,    
      "description": "Слова паразиты",  
      "common": true,    
      "updated_at": null
    },  
    {   
      "id": 5,    
      "description": "Вредные привычки",    
      "common": true,    
      "updated_at": null
    },   
    {
       "id": 26,    
       "description": "one more  test event type",      
       "common": false,     
       "updated_at": "2017-01-04 14:04:46"      
    }   
  ]     
}  

**error responce example:**     
{"error": "token_invalid"}   
status: 400 Bad Request

{"error": "token_not_provided"}   
status: 400 Bad Request

{"error":"could_not_get_event_types"}     
status: 500 Internal Server Error   

## function: add_event_type_remote      

**method:** POST    
**parameters:**     
    (string) description - required|max:255|unique for this user and commons  
    
**return:** (object) new event_type  

**request example:**    
http://localhost:8000/api/v1/add_event_type_remote       
**form-data:**      
    description:      "new event_type"     
            
**successfull responce example:**   
{   
    "event_type":{
        "id":26,    
        "description":"one more test event type",   
        "common":false   
     }  
}      

**error responce example:**     
{   
    "error":{   
        "description":["The description field is required."]    
    }   
}   
status: 422 Unprocessable Entity    

{   
     "error":{  
        "description":["The description has already been taken."]   
     }  
}   
status: 422 Unprocessable Entity    

{"error":"could_not_add_event_type"}     
status: 500 Internal Server Error

## function: destroy_event_type_remote      

**method:** DELETE    
**parameters:**     
    (integer) event_type_id - required|integer
    (string) description - required|max:255  
  
**return:**   (string) result   

**request example:**    
http://localhost:8000/api/v1/destroy_event_type_remote/27/new test event type   

**successfull responce example:**   
{   
    "warning":"need_to_synchronize_event_types",    
    "result":"success"  
}   
warning возникает если совпадает description и user_id но почему-то не совпадает id самого event_type   
при этом, наверное, сдедует запросить свежие данные - get_event_types_remote    
несмотря на warning найденная запись удаляется      

{"result":"success"}   

**error responce example:**     

{   
     "error":{  
        "description":["The description field is required."]   
     }  
}   
status: 422 Unprocessable Entity    

{   
     "error":{  
        "id":["The id must be an integer"]   
     }  
}   
status: 422 Unprocessable Entity    

{"error":"event_type_not_found"}    
status: 404 Not Found   

{"error":"can_not_destroy_common_event_type"}   
status: 422 Unprocessable Entity    

{"error":"can_not_destroy_alien_event_type"}  
status: 422 Unprocessable Entity    

{"error":"could_not_destroy_event_type"}     
status: 500 Internal Server Error   

## function: update_event_type_remote      

**method:** POST    
**parameters:**     
    (integer) event_type_id - required|integer
    (string) old_description - required|max:255  
    (string) new_description - required|max:255  
  
**return:**   (string) result   

**request example:**    
http://localhost:8000/api/v1/update_event_type_remote 
**form-data:**      
     id:      26    
     old_description: "Мысли о лишнем и недостаточном весе" 
     new_description: "event type updated"
    
**successfull responce example:**   
{   
    "warning":"need_to_synchronize_event_types",    
    "result":"success"  
}   
warning возникает если совпадает old_description и user_id но почему-то не совпадает id самого event_type   
при этом, наверное, сдедует запросить свежие данные - get_event_types_remote    
несмотря на warning найденная запись update'тся       

{"result":"success"}   

**error responce example:**     

{   
     "error":{  
        "description":["The old description field is required."]   
     }  
}   
status: 422 Unprocessable Entity    

{   
     "error":{  
        "id":["The id must be an integer"]   
     }  
}   
status: 422 Unprocessable Entity    

{"error":"event_type_not_found"}    
status: 404 Not Found   

{"error":"can_not_update_common_event_type"}   
status: 422 Unprocessable Entity    

{"error":"can_not_update_alien_event_type"}  
status: 422 Unprocessable Entity    

{   
    "error":"could_not_update_event_type",    
    "warning":"try_to_synchronize"
}     
status: 500 Internal Server Error