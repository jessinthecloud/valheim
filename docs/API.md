# API

This API allows requesting Valheim item and recipe information. Currently only `GET` requests are supported

# GET
## Items
### A listing of all items

https://valheim.recipes/api/v1/items

#### Request
```
GET /api/v1/items HTTP/1.1
Host: valheim.recipes
```

#### Response

##### Body
```json
{
    "data": [
        {
            "id": 240,
            "name": "Abyssal harpoon",
            "slug": "abyssal-harpoon",
            "raw_name": "Abyssal harpoon",
            "raw_slug": "abyssal-harpoon",
            "true_name": "SpearChitin",
            "true_slug": "spearchitin",
            "var_name": "$item_spear_chitin",
            "recipe_id": 105,
            "shared_data_id": 240,
            "created_at": "2021-05-09T20:29:57.000000Z",
            "updated_at": "2021-05-09T20:29:57.000000Z" 
        },
        {
            "id": 241,
            "name": "Ancient bark spear",
            "slug": "ancient-bark-spear",
            "raw_name": "Ancient bark spear",
            "raw_slug": "ancient-bark-spear",
            "true_name": "SpearElderbark",
            "true_slug": "spearelderbark",
            "var_name": "$item_spear_ancientbark",
            "recipe_id": 106,
            "shared_data_id": 241,
            "updated_at": "2021-05-09T20:29:57.000000Z"
        }
    ]
}
```

##### Headers
`Content-Type` | `application/json`

---

### Specific item by slug
https://valheim.recipes/api/v1/items/{slug}

#### Path Variables
`slug` | `string` (Required) slug of the item, based on item name

#### Request
```
GET /api/v1/items/abyssal-harpoon HTTP/1.1
Host: valheim.recipes
```

#### Response
##### Body 
```json
{
    "data": {
        "id": 240,
        "name": "Abyssal harpoon",
        "slug": "abyssal-harpoon",
        "raw_name": "Abyssal harpoon",
        "raw_slug": "abyssal-harpoon",
        "true_name": "SpearChitin",
        "true_slug": "spearchitin",
        "var_name": "$item_spear_chitin",
        "recipe_id": 105,
        "shared_data_id": 240,
        "created_at": "2021-05-09T20:29:57.000000Z",
        "updated_at": "2021-05-09T20:29:57.000000Z"
    }
}
```
##### Headers
`Content-Type` | `application/json`

---

### Specific item by id
https://valheim.recipes/api/v1/items/{id}

#### Path Variables
`id` | `int` (Required) Database ID of the item

#### Request
```
GET /api/v1/items/240 HTTP/1.1
Host: valheim.recipes
```

#### Response
##### Body 
```json
{
    "data": {
        "id": 240,
        "name": "Abyssal harpoon",
        "slug": "abyssal-harpoon",
        "raw_name": "Abyssal harpoon",
        "raw_slug": "abyssal-harpoon",
        "true_name": "SpearChitin",
        "true_slug": "spearchitin",
        "var_name": "$item_spear_chitin",
        "recipe_id": 105,
        "shared_data_id": 240,
        "created_at": "2021-05-09T20:29:57.000000Z",
        "updated_at": "2021-05-09T20:29:57.000000Z"
    }
}
```
##### Headers
`Content-Type` | `application/json`

---
## Recipes
### A listing of all recipes

https://valheim.recipes/api/v1/recipes

#### Request
```
GET /api/v1/recipes HTTP/1.1
Host: valheim.recipes
```

#### Response

##### Body
```json
{
  "data": [
    {
      "id": 105,
      "name": "Abyssal harpoon",
      "slug": "abyssal-harpoon",
      "raw_name": "Abyssal harpoon",
      "raw_slug": "abyssal-harpoon",
      "true_name": "Recipe_SpearChitin",
      "true_slug": "recipe-spearchitin",
      "var_name": "$item_spear_chitin",
      "crafting_station_id": 1,
      "repair_station_id": null,
      "enabled": 1,
      "amount": 1,
      "min_station_level": 2,
      "created_at": "2021-05-09T20:29:57.000000Z",
      "updated_at": "2021-05-09T20:29:57.000000Z"
    },
    {
      "id": 106,
      "name": "Ancient bark spear",
      "slug": "ancient-bark-spear",
      "raw_name": "Ancient bark spear",
      "raw_slug": "ancient-bark-spear",
      "true_name": "Recipe_SpearElderbark",
      "true_slug": "recipe-spearelderbark",
      "var_name": "$item_spear_ancientbark",
      "crafting_station_id": 2,
      "repair_station_id": null,
      "enabled": 1,
      "amount": 1,
      "min_station_level": 3,
      "created_at": "2021-05-09T20:29:57.000000Z",
      "updated_at": "2021-05-09T20:29:57.000000Z"
    }
  ]
}
```

##### Headers
`Content-Type` | `application/json`

---

### Specific recipe fetched by slug
https://valheim.recipes/api/v1/recipes/{slug}

#### Path Variables
`slug` | `string` (Required) slug of the recipe, based on recipe name

#### Request
```
GET /api/v1/recipes/abyssal-harpoon HTTP/1.1
Host: valheim.recipes
```

#### Response
##### Body 
```json
{
 "data": {
  "id": 105,
  "name": "Abyssal harpoon",
  "slug": "abyssal-harpoon",
  "raw_name": "Abyssal harpoon",
  "raw_slug": "abyssal-harpoon",
  "true_name": "Recipe_SpearChitin",
  "true_slug": "recipe-spearchitin",
  "var_name": "$item_spear_chitin",
  "crafting_station_id": 1,
  "repair_station_id": null,
  "enabled": 1,
  "amount": 1,
  "min_station_level": 2,
  "created_at": "2021-05-09T20:29:57.000000Z",
  "updated_at": "2021-05-09T20:29:57.000000Z"
 }
}
```
##### Headers
`Content-Type` | `application/json`

---

### Specific recipe fetched by id
https://valheim.recipes/api/v1/recipes/{id}

#### Path Variables
`id` | `int` (Required) Database ID of the recipe

#### Request
```
GET /api/v1/recipes/105 HTTP/1.1
Host: valheim.recipes
```

#### Response
##### Body 
```json
{
 "data": {
  "id": 105,
  "name": "Abyssal harpoon",
  "slug": "abyssal-harpoon",
  "raw_name": "Abyssal harpoon",
  "raw_slug": "abyssal-harpoon",
  "true_name": "Recipe_SpearChitin",
  "true_slug": "recipe-spearchitin",
  "var_name": "$item_spear_chitin",
  "crafting_station_id": 1,
  "repair_station_id": null,
  "enabled": 1,
  "amount": 1,
  "min_station_level": 2,
  "created_at": "2021-05-09T20:29:57.000000Z",
  "updated_at": "2021-05-09T20:29:57.000000Z"
 }
}
```
##### Headers
`Content-Type` | `application/json`
