# Hyperion

Hyperion is an event booking web application. Users can discover events, book tickets, and manage their account. Organizers can create and manage their own events. The project covers the full lifecycle from local development to production deployment on a VPS.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2+), JWT authentication |
| Frontend | Static HTML, Bootstrap 5, CSS |
| Database | MySQL 8 |
| Containerization | Docker, Docker Compose |
| Orchestration | Kubernetes (K3s), Traefik ingress, Let's Encrypt TLS |
| Infrastructure automation | Ansible |
| Networking | Tailscale, UFW |

## Repository layout

```
.
├── client/                  Static frontend (HTML/CSS/Bootstrap)
│   └── src/
│       ├── index.html       Landing page
│       └── Components/      Application views (SignIn, SignUp, Events, ...)
├── server/
│   └── HyperionServer/      Laravel application
│       ├── app/
│       │   ├── Http/        Controllers, Middleware
│       │   └── Models/      Eloquent models
│       └── routes/web.php   API route definitions
├── Schemas.sql              Database DDL (reference schema)
├── Dockerfile               Application image build
├── docker-compose.yml       Local stack (app + MySQL)
├── deployment.yaml          Kubernetes manifests
├── playbook.yml             Ansible VPS provisioning
├── deploy-app.yml           Ansible application deployment
├── hosts.ini                Ansible inventory (template)
└── docs/                    Extended documentation (wiki)
```

## Quick start

See [docs/development.md](docs/development.md) for the full local development setup.

**Minimum requirements:** Docker, Docker Compose.

```bash
cp server/HyperionServer/.env.example server/HyperionServer/.env
# Edit .env: set DB_CONNECTION=mysql, DB_HOST=db, DB_DATABASE=Hyperion, DB_USERNAME=root, DB_PASSWORD=<password>
docker compose up -d
```

The application is available at `http://localhost:8000`.

## Production deployment

Deployment targets a VPS running K3s. Two Ansible playbooks handle the full process:

1. `playbook.yml` — provisions the VPS (UFW, Tailscale, K3s, Traefik with Let's Encrypt).
2. `deploy-app.yml` — pushes the Kubernetes manifests and waits for the rollout.

See [docs/deployment.md](docs/deployment.md) for the complete procedure.

## Documentation

| Document | Contents |
|---|---|
| [docs/architecture.md](docs/architecture.md) | System architecture and component interactions |
| [docs/api.md](docs/api.md) | Full API reference |
| [docs/database.md](docs/database.md) | Database schema and entity relationships |
| [docs/deployment.md](docs/deployment.md) | Docker, Kubernetes, and Ansible deployment guide |
| [docs/development.md](docs/development.md) | Local development setup |
