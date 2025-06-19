
app:
image: bitnami/laravel
container_name: Hyperion
restart: unless-stopped
working_dir: /app
volumes:
- ./:/app
ports:
- "8000:8000"
depends_on:
- db
environment:
- DB_CONNECTION=mysql
- DB_HOST=db
- DB_PORT=3300
- DB_DATABASE=Hyperion
- DB_USERNAME=root
- DB_PASSWORD=afterlife