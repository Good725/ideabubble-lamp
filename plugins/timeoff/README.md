### List requests

`GET http://{{host}}/api/timeoff/`

Available params: staff_id, manager_id, department_id, status, type, period_start_date, period_end_date
Sorting: order_by, order_dir

Response:

```
{
"items": [
    {
        "id": "2",
        "staff": {
            "id": "47",
            "name": "John",
            "position": "janitor"
        },
    "status": "pending",
    "type": "annual",
    "period": [
        "2018-08-01 01:00:00",
        "2018-08-31"
    ],
    "notes": []
    },
    ... items ...
],
"status": "success",
"error": null
}
```

### List requests in datatables format

`GET http://{{host}}/api/timeoff/items_datatable`

Available params: staff_id, manager_id, department_id, status, type, period_start_date, period_end_date
Sorting: order_by, order_dir

Response:

```
{
  "iTotalDisplayRecords": 5,
  "iTotalRecords": "5",
  "aaData": [
    [
      "10",
      "John",
      "Accounting",
      "janitor",
      "2018-08-01",
      "2018-08-31",
      "annual",
      30,
      "pending",
      "",
      "<button\n                        type=\"button\" class=\"btn-link timeoff-requests-table-view\"\n                        data-id=\"10\" data-leave_type=\"annual\"\n                        >view<\/button>"
    ],
    ... items ...
  ],
  "sEcho": 1
}
```

### Get Single request

`GET http://{{host}}/api/timeoff/getrequest?id={{id}}`

Response:

```
{
  "id": "10",
  "staff": {
    "id": "47",
    "name": "John",
    "position": "janitor"
  },
  "status": "pending",
  "type": "annual",
  "period": [
    "2018-08-01 01:00:00",
    "2018-08-31"
  ],
  "notes": [
    {
      "id": "11",
      "created_at": "2018-09-03 09:46:24",
      "user_id": "47",
      "name": "John",
      "content": "helloworld"
    },
    ... items ...
  ]
}
```

### Post new request

`POST http://{{host}}/api/timeoff/submit`

Content-Type: application/x-www-form-urlencoded

`period_start_date=1533081600&period_end_date=1535673600&type=annual&note=helloworld&department_id=1`

Response:

````
{
    "status": "success"
}
````


### Approve request ###

`POST http://{{host}}/api/timeoff/approve`

Content-Type: application/x-www-form-urlencoded

`id=1&note=helloworld`

Response:

````
{
    "status": "success"
}
````



### Decline request ###

`POST http://{{host}}/api/timeoff/decline`

Content-Type: application/x-www-form-urlencoded

`_user_id={{manager}}&id=1&note=helloworld`

Response:

````
{
    "status": "success"
}
````

### View conflicts for task with id = 2 ###

`GET http://{{host}}/api/timeoff/conflicts/?id=2`

Response:

```
{
"items": [
    {
        "id": "2",
        "staff": {
            "id": "47",
            "name": "John",
            "position": "janitor"
        },
    "status": "pending",
    "type": "annual",
    "period": [
        "2018-08-01 01:00:00",
        "2018-08-31"
    ],
    "notes": []
    },
    ... items ...
],
"status": "success",
"error": null
}
```

### List all available departments

GET http://{{host}}/api/timeoff/departments

Response:

````
{
  "items": [
    {
      "id": "1",
      "name": "Accounting"
    },
    {
      "id": "2",
      "name": "Human Resources"
    },
    {
      "id": "3",
      "name": "Security"
    },
    {
      "id": "4",
      "name": "Cleaning"
    }
  ],
  "status": "success",
  "error": null
}
````

