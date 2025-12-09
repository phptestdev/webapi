# <p style="color: yellow;">WebAPI</p>
WebAPI Repository

## <p style="color: green;">Install Project</p>

To install project make actions described bellow:

1. Make sure **Docker** is installed and running on your machine.

2. Make sure **Docker Compose** is installed and running on your machine.

3. Make sure **Git** is installed and running on your machine.

4. Make sure port **8080** is open and available to use on your machine.

5. Execute command 

**git clone https://github.com/phptestdev/webapi.git**

to clone the repository.

6. Go to the **webapi** directory.

7. Execute command 

**sudo docker compose -f compose.prod.yaml up --build -d**

to build images and running containers.

When all containers will be running execute command

**sudo docker compose -f compose.prod.yaml exec -it php-fpm php /var/www/html/artisan migrate**

to make DB migration.

8. Use an API as provided below

You can use ready to use Postman Collection. Import WebAPI.postman_collection.json file to your Postman app.

---

## <p style="color: green;">Using REST API</p>

Use this REST API for managing webserver & and virtual host actions.

---

### Base URL
http://127.0.0.1:8080/api

---

### Authentication

Most endpoints require a **Bearer Token** obtained after logging in.

Use the header:

Authorization: Bearer <token>

---

### Endpoints

#### User Endpoints

##### <p style="color: #99ff99;">POST /user/register</p>
Registers a new user.

**URL:**
http://127.0.0.1:8080/api/user/register

**Body (form-data):**

| Key | Value |
|-------|--------|
| email | test@test.com |
| password | password12345 |
| name | Test |



##### <p style="color: #99ff99;">POST /user/login</p>
Logs in an existing user.

**URL:**
http://127.0.0.1:8080/api/user/login

**Body (form-data):**

| Key | Value |
|-------|--------|
| email | test@test.com |
| password | password12345 |

---

#### Webserver Endpoints

> All endpoints below require **Authorization: Bearer <token>**

##### <p style="color: #99ff99;">GET /webserver/start</p>
Start the webserver.

http://127.0.0.1:8080/api/webserver/start

##### <p style="color: #99ff99;">GET /webserver/stop</p>
Stop the webserver.

http://127.0.0.1:8080/api/webserver/stop

##### <p style="color: #99ff99;">GET /webserver/restart</p>
Restart the webserver.

http://127.0.0.1:8080/api/webserver/restart

##### <p style="color: #99ff99;">GET /webserver/reload</p>
Reload the webserver.

http://127.0.0.1:8080/api/webserver/reload

---

#### Virtual Host Endpoints

> All endpoints require **Authorization: Bearer <token>**

##### <p style="color: #99ff99;">GET /vhosts</p>
Get a list of virtual hosts.

http://127.0.0.1:8080/api/vhosts?page=1

**Query Params:**

| Param | Value |
|-------|--------|
| page  | 1      |



##### <p style="color: #99ff99;">GET /vhost/{id}</p>
Get data for a specific virtual host.

http://127.0.0.1:8080/api/vhost/2



##### <p style="color: #99ff99;">POST /vhost/create</p>
Create a new virtual host.

http://127.0.0.1:8080/api/vhost/create

**Body (form-data):**

| Key  | Value        |
|------|--------------|
| name | domain.local |



##### <p style="color: #99ff99;">DELETE /vhost/delete</p>
Delete an existing virtual host.

http://127.0.0.1:8080/api/vhost/delete

**Body (form-data):**

| Key | Value |
|-----|--------|
| id  | 1      |