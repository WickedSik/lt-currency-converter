# Let's Talk Currency Convertor

> **Author**: Jurriën Dokter

## Installation

1. Checkout repo
2. Update `.env`
    - Please note that the database name should be changed in both `MARIADB_DATABASE` and `DATABASE_URL`
3. Execute `docker compose up -d`
4. Execute the following commands once the docker images are up;

```bash
# Enter shell container
docker compose exec php-fpm bash
# In the container
cd /var/www/html
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

> Do not forget to add IP addresses to the trusted ip addresses:
> 
> `php bin/console app:add-ip-to-user [user] [ip]`

You can now navigate to [localhost:8080](http://localhost:8080) to use the application

### Admin user

> Since there is no admin interface, admin users have little extra use.

```text
Username: tom_admin
Password: ltcc-admin

Username: jane_admin
Password: ltcc-admin
```

### Regular user

```text
Username: john_user
Password: ltcc
```

## Mailing

The Mailhog container is included as well and configured to handle the emails send by Symfony. The webclient can be
reached [here](http://localhost:18025)

## Elasticsearch & Kibana

Both Elasticsearch and Kibana are included in the docker. The Currency Rates will be injected into Elasticsearch
whenever they are updated so the progression and trend can be extrapolated. Since there is no historical data
there has not been any charts or history-diagrams added, but running the project for a longer time will allow for this
to be done. 

Elasticsearch can be reached at : http://localhost:9200  
Kibana can be reached at : http://localhost:5601