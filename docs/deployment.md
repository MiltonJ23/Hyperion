# Deployment

Two deployment paths are provided: Docker Compose for local use and Kubernetes on a VPS for production.

---

## Docker Compose (local / staging)

### Prerequisites

- Docker Engine
- Docker Compose v2

### Steps

1. Copy the example environment file and configure it:

```bash
cp server/HyperionServer/.env.example server/HyperionServer/.env
```

Edit `.env` and set:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=Hyperion
DB_USERNAME=root
DB_PASSWORD=<password>
JWT_SECRET=<generated_secret>
```

Generate the JWT secret:

```bash
docker compose run --rm app php artisan jwt:secret
```

2. Start the stack:

```bash
docker compose up -d
```

This starts two containers:

| Container | Image | Port |
|---|---|---|
| hyperion_booking_app | miltonj23/hyperion-booking-app:v1.0 | 8000 |
| hyperion_db | mysql:latest | 33066 (host) → 3306 |

The database data is persisted in the `hyperion_db_data` Docker volume.

3. Run migrations:

```bash
docker compose exec app php artisan migrate
```

4. The application is available at `http://localhost:8000`.

---

## Kubernetes on a VPS (production)

The production environment is a single VPS running K3s. Ansible automates the full provisioning and deployment.

### Prerequisites

- Ansible installed on the control machine.
- SSH access to the VPS with a dedicated key.
- A Tailscale auth key.
- A Docker Hub image published as `miltonj23/hyperion-booking-app:v1.0`.

### Inventory configuration

Edit `hosts.ini` and replace the placeholders:

```ini
[production]
vps_hyperion ansible_host=<vps_ip> ansible_user=<user> ansible_ssh_private_key_file=<path_to_key>
```

### Step 1 — Provision the VPS

`playbook.yml` configures the VPS from a fresh Ubuntu installation:

- Updates system packages.
- Installs and enables UFW (allows SSH, HTTP/80, HTTPS/443, and all Tailscale interface traffic).
- Installs Tailscale and connects the machine using the provided auth key.
- Installs K3s (lightweight Kubernetes).
- Deploys a Traefik `HelmChartConfig` to enable ACME TLS certificates via the TLS challenge.

Set the required variables before running:

```bash
# In playbook.yml vars section:
tailscale_authkey: "<your_tailscale_authkey>"
email_letsencrypt: "<your_email>"
```

Run:

```bash
ansible-playbook -i hosts.ini playbook.yml
```

### Step 2 — Deploy the application

`deploy-app.yml` copies `deployment.yaml` to the VPS and applies it with `kubectl`.

```bash
ansible-playbook -i hosts.ini deploy-app.yml
```

The playbook:
1. Copies `deployment.yaml` to `/tmp/hyperion-deployment.yaml` on the VPS.
2. Applies the manifest with `kubectl apply`.
3. Waits for the deployment rollout to complete (timeout: 5 minutes).

### Kubernetes manifests (deployment.yaml)

The manifest creates the following resources in the default namespace:

| Resource | Kind | Description |
|---|---|---|
| mysql-pvc | PersistentVolumeClaim | 1 Gi storage for MySQL data |
| hyperion-db | Deployment | MySQL 8.0 container |
| hyperion-db-service | Service | Internal ClusterIP on port 3306 |
| hyperion-booking-app | Deployment | Laravel app, 2 initial replicas |
| hyperion-booking-app-service | Service | ClusterIP on port 80 → 8000 |
| hyperion-ingress | Ingress | Traefik, TLS for `hyperion.io` |
| hyperion-app-hpa | HorizontalPodAutoscaler | Scales 2–10 replicas at 50% CPU |

**Health checks** are configured on the Laravel deployment using `GET /api/events`:
- Liveness probe: starts after 30 s, runs every 10 s.
- Readiness probe: starts after 5 s, runs every 5 s.

**Resource limits** per pod: CPU request 200m, limit 500m.

### TLS

Traefik issues TLS certificates automatically via Let's Encrypt. The domain `hyperion.io` must point to the VPS public IP. The certificate is stored in `/data/acme.json` inside the Traefik pod.

### Updating the application

1. Build and push a new image:

```bash
docker build -t miltonj23/hyperion-booking-app:<new_tag> .
docker push miltonj23/hyperion-booking-app:<new_tag>
```

2. Update the image reference in `deployment.yaml`, then re-run the deploy playbook.

---

## Building the Docker image manually

The `Dockerfile` is at the repository root:

```dockerfile
FROM bitnami/laravel:latest
WORKDIR /app
COPY ./server/HyperionServer /app
RUN composer install --no-interaction --optimize-autoloader
RUN chmod -R 777 storage bootstrap/cache
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

Build:

```bash
docker build -t miltonj23/hyperion-booking-app:v1.0 .
```
