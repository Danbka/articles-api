###Assignment:

Headless (no UI) CMS for managing articles (CRUD).

Each article has its ID, title, body and timestamps (on creation and update).

Managing articles is the typical set of CRUD calls including reading one and all the articles. Creating/updating/deleting data is possible only if a secret token is provided (can be just one static token).

For reading all the articles, the endpoint must allow specifying a field to sort by including whether in an ascending or descending order + basic limit/offset pagination.

The whole client-server communication must be in a JSON format.

**Optional:**
Make the architecture ready to communicate in different formats like XML (no need for the implementation of the additional format, just make it extensible).

###Installation

Clone repository

```
git clone 
```

Install dependencies
```
composer install
```

Set up .env variables

### Swagger documentation
```
/doc
```

###Run tests
php bin/phpunit