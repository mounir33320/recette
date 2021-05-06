# API participative de recettes - Documentation

Version 0.0.1

## Résumé
Cette API participative permet de partager des recettes entre utilisateurs.

## Endpoints
### Recettes
#### GET /recettes
Récupérer une collection de recettes. 
#####order :
- cout
- nom
- nbPersonne
- tempsPreparation
- dateCreation

_Exemple : Réponse HTTP 200_
```
[
    {
        "id": 1,
        "tempsPreparation": 30,
        "cout": 15,
        "nbPersonne": 4,
        "dateCreation": "2021-05-04T07:57:13+00:00",
        "nom": "Blanquette",
        "public": true
    }
]
```

#### GET /recettes/{id}
Récupérer un item de recette. 

**id (integer) requis**

_Exemple : Réponse HTTP 200_
```
[
    {
        "id": 1,
        "tempsPreparation": 30,
        "cout": 15,
        "nbPersonne": 4,
        "dateCreation": "2021-05-04T07:57:13+00:00",
        "nom": "Blanquette",
        "public": true
    }
]
```

#### POST /recettes
Créer un item de recette.

_Exemple : Réponse HTTP 201_
```
[
    {
        "id": 1,
        "tempsPreparation": 30,
        "cout": 15,
        "nbPersonne": 4,
        "dateCreation": "2021-05-04T07:57:13+00:00",
        "nom": "Blanquette",
        "public": true
    }
]
```

#### PUT /recettes/{id}
Modifier totalement un item de recette.

**id (integer) requis**

_Exemple : Réponse HTTP 200_
```
[
    {
        "id": 1,
        "tempsPreparation": 30,
        "cout": 15,
        "nbPersonne": 4,
        "dateCreation": "2021-05-04T07:57:13+00:00",
        "nom": "Blanquette",
        "public": true
    }
]
```

#### PATCH /recettes/{id}
Modifier partiellement un item de recette.

**id (integer) requis**

_Exemple : Réponse HTTP 200_
```
[
    {
        "id": 1,
        "tempsPreparation": 30,
        "cout": 15,
        "nbPersonne": 4,
        "dateCreation": "2021-05-04T07:57:13+00:00",
        "nom": "Blanquette",
        "public": true
    }
]
```

#### DELETE /recettes/{id}
Supprimer un item de recette.

**id (integer) requis**

_Exemple : Réponse HTTP 204_
```
[
    {
        "message": "Success"
    }
]
```