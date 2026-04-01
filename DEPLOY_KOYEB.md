# Deploiement Koyeb

Cette base prepare un deploiement "gratuit d'abord" sur Koyeb avec:

- 1 service web Laravel
- 1 service Reverb pour le temps reel
- 1 base PostgreSQL Koyeb

Les references officielles Koyeb utiles:

- Quick start: https://www.koyeb.com/docs/deploy
- Health checks: https://www.koyeb.com/docs/run-and-scale/health-checks
- Databases: https://www.koyeb.com/docs/databases
- Docker images / Dockerfile: https://www.koyeb.com/docs/build-and-deploy/prebuilt-docker-images

## 1. Fichiers prepares

- `Dockerfile`
- `.dockerignore`
- `docker/start-web.sh`
- `docker/start-reverb.sh`
- `.env.koyeb.example`

## 2. Base de donnees

Dans Koyeb:

1. Creez une base PostgreSQL geree.
2. Recuperez:
   - host
   - port
   - database
   - username
   - password
3. Injectez-les dans les variables `DB_*`.

Le projet est deja compatible `pgsql`.

## 3. Variables d environnement

Base minimale pour les deux services:

- `APP_NAME`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<votre-service-web>.koyeb.app`
- `APP_KEY`
- `BROADCAST_CONNECTION=reverb`
- `DB_CONNECTION=pgsql`
- `DB_HOST`
- `DB_PORT=5432`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_SSLMODE=require`
- `SESSION_DRIVER=cookie`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

Variables Reverb partagees par les deux services:

- `REVERB_APP_ID`
- `REVERB_APP_KEY`
- `REVERB_APP_SECRET`
- `REVERB_HOST=<domaine-public-du-service-reverb>`
- `REVERB_PORT=443`
- `REVERB_SCHEME=https`
- `REVERB_SERVER_HOST=0.0.0.0`
- `REVERB_SERVER_PORT=8080`
- `VITE_REVERB_APP_KEY=${REVERB_APP_KEY}`
- `VITE_REVERB_HOST=${REVERB_HOST}`
- `VITE_REVERB_PORT=${REVERB_PORT}`
- `VITE_REVERB_SCHEME=${REVERB_SCHEME}`

Important:

- `REVERB_HOST` doit pointer vers le domaine public du service Reverb.
- `REVERB_PORT` vaut `443` car le navigateur passera par HTTPS/WSS.
- `REVERB_SERVER_PORT` vaut `8080` car le conteneur ecoute en interne sur ce port.

## 4. Service Web

Creation du service:

- Deployment method: depuis le repo Git avec `Dockerfile`
- Type: Web Service
- Port expose: `8000`
- Health check HTTP:
  - path: `/up`
  - port: `8000`

Commande de demarrage:

```sh
start-web
```

Ce script:

- genere la cle si besoin
- tente `storage:link`
- lance les migrations
- met en cache config/routes/views
- demarre Laravel sur `0.0.0.0:${PORT:-8000}`

## 5. Service Reverb

Creation du second service:

- Meme repo
- Meme `Dockerfile`
- Type: Web Service
- Port expose: `8080`
- Health check TCP sur `8080`

Commande de demarrage:

```sh
start-reverb
```

Le service Reverb doit etre public pour que le navigateur puisse s'y connecter en WebSocket.

## 6. Ordre conseille

1. Creez la base PostgreSQL.
2. Creez le service Reverb.
3. Notez son domaine public.
4. Creez le service Web avec `REVERB_HOST` pointe sur ce domaine.
5. Mettez les memes secrets `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` sur les deux services.
6. Redeployez les deux services.

## 7. Valeurs a generer

Pour generer une vraie cle et des secrets:

```sh
php artisan key:generate --show
```

Pour les variables Reverb, utilisez des valeurs longues aleatoires.

## 8. Verification apres deploiement

- `https://<web-service>.koyeb.app/up` doit repondre correctement.
- La page de messagerie doit se charger.
- Deux navigateurs sur la meme conversation doivent recevoir les messages sans reload.
- Les appels audio/video doivent ouvrir la salle Jitsi.

## 9. Limites du mode gratuit

Inference a partir de la doc Koyeb actuelle:

- Koyeb annonce au moins une instance `free` avec `512MB RAM`, `0.1 vCPU`, `2GB SSD`.
- Selon votre compte et les conditions en vigueur, verifier dans le dashboard si vous pouvez garder web + reverb gratuitement en meme temps.

Si la limite gratuite ne couvre pas les deux services simultanement, le plan B le plus simple est:

- garder seulement le service web sur Koyeb
- utiliser temporairement Pusher/Ably pour le WebSocket

## 10. Prochaine etape utile

Si vous voulez, je peux maintenant vous preparer:

- un `koyeb.yaml` si vous voulez piloter les services par configuration
- un durcissement production du service web (Caddy/Nginx + PHP-FPM)
- un mode "worker" separe si vous passez plus tard aux queues asynchrones
