# Architecture

## Overview

Hyperion is structured as a monorepo containing a static frontend, a Laravel REST API backend, and all infrastructure configuration needed to run the application in production.

```
Browser
  |
  | HTTP/HTTPS
  v
Traefik (ingress, TLS termination)
  |
  v
Laravel application (port 8000)
  |
  v
MySQL 8 (port 3306, internal)
```

In production the traffic path is: client browser → Traefik ingress controller → Laravel pods → MySQL pod.

## Components

### Frontend (client/)

A set of static HTML files served directly by the Laravel application from its `public/` directory. Bootstrap 5 provides the UI framework. No JavaScript build step is required.

Pages:
- `index.html` — landing page
- `Components/SignIn.html`, `SignUp.html` — authentication
- `Components/DiscoverEvents.html`, `EventDetails.html` — event browsing
- `Components/HomePage.html` — authenticated home
- `Components/AccountHome.html`, `AccountProfile.html`, `AccountBookings.html`, `AccountPaymentMethods.html` — user account area
- `Components/CreateEvent.html` — organizer event creation
- `Components/ViewTicket.html` — ticket display
- `Components/Pricing.html`, `Contact.html`, `TermsAndConditions.html` — static content

### Backend (server/HyperionServer/)

A Laravel 12 application exposing a JSON API. Authentication is handled with JWT (tymon/jwt-auth). All state is stored in MySQL; images are stored on the local filesystem under `storage/app/public`.

Key packages:
- `laravel/framework` ^12.0
- `tymon/jwt-auth` ^2.2

### Database

MySQL 8 with ten tables. The full schema is in `Schemas.sql`. See [database.md](database.md) for details.

### Infrastructure

| File | Purpose |
|---|---|
| `Dockerfile` | Builds the production application image from `bitnami/laravel:latest` |
| `docker-compose.yml` | Local stack: `app` container + `db` MySQL container |
| `deployment.yaml` | Kubernetes manifests: PVC, MySQL Deployment/Service, App Deployment/Service, Ingress, HPA |
| `playbook.yml` | Ansible: VPS provisioning (UFW, Tailscale, K3s, Traefik) |
| `deploy-app.yml` | Ansible: copies and applies `deployment.yaml` on the cluster |
| `hosts.ini` | Ansible inventory template |

## Production infrastructure

The production environment runs on a single VPS managed by Ansible.

- **K3s** — lightweight Kubernetes distribution.
- **Traefik** — ingress controller bundled with K3s, configured via `HelmChartConfig` to issue TLS certificates automatically through Let's Encrypt (ACME TLS challenge).
- **Tailscale** — overlay VPN used to reach the VPS securely over its Tailscale IP. UFW allows all Tailscale interface traffic.
- **HPA** — Horizontal Pod Autoscaler scales the application deployment between 2 and 10 replicas based on CPU utilization (threshold: 50%).

## Request flow (production)

1. DNS for `hyperion.io` resolves to the VPS public IP.
2. Traefik terminates TLS and forwards the request to the `hyperion-booking-app-service` on port 80.
3. The service load-balances across the running Laravel pods (port 8000).
4. The Laravel pod processes the request and queries the `hyperion-db-service` (MySQL) on port 3306.
5. The response is returned to the client.
