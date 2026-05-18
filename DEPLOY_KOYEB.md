# Deploiement Koyeb

Cette base prepare deux chemins Koyeb:

- mode gratuit strict: 1 service web Laravel + 1 base PostgreSQL Koyeb
- mode complet: 1 service web Laravel + 1 service Reverb + 1 base PostgreSQL Koyeb

Les references officielles Koyeb utiles:

- Quick start: https://www.koyeb.com/docs/deploy
- Health checks: https://www.koyeb.com/docs/run-and-scale/health-checks
- Databases: https://www.koyeb.com/docs/databases
- Docker images / Dockerfile: https://www.koyeb.com/docs/build-and-deploy/prebuilt-docker-images

## Decision rapide apres Laravel Cloud

Si votre essai Laravel Cloud est termine et que vous voulez repartir sans paiement immediat, commencez par le mode gratuit strict.

Koyeb indique actuellement qu'une organisation a droit a:

- un service web `free` avec 512MB RAM, 0.1 vCPU et 2GB SSD
- une base PostgreSQL gratuite limitee a 5 heures actives/mois et 1GB de stockage

Comme un service Reverb public consommerait un deuxieme service web, il ne rentre pas dans le mode gratuit strict. Le projet reste utilisable sans Reverb: la messagerie a un fallback polling toutes les 4 secondes.

Passez au mode complet seulement si vous acceptez un deuxieme service payant ou si Koyeb vous laisse deployer plus d'un service gratuit sur votre compte.

## 1. Fichiers prepares

- `Dockerfile`
- `.dockerignore`
- `docker/start-web.sh`
- `docker/start-reverb.sh`
- `.env.koyeb.example`
- `.env.koyeb.free.example`

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

### Mode gratuit strict

Utilisez `.env.koyeb.free.example` comme base.

Variables minimales du service web:

- `APP_NAME`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<votre-service-web>.koyeb.app`
- `APP_KEY`
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
- `BROADCAST_CONNECTION=log`
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI=https://<votre-service-web>.koyeb.app/auth/google/callback`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`
- `ADMIN_NAME=Administrateur`

Dans ce mode, ne creez pas de service Reverb et laissez les variables `REVERB_*` vides ou absentes. La messagerie continuera a se rafraichir par polling.

### Mode complet avec Reverb

Utilisez `.env.koyeb.example` comme base.

Variables de base pour les deux services:

- les variables du service web ci-dessus
- `BROADCAST_CONNECTION=reverb` a la place de `BROADCAST_CONNECTION=log`

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
- Instance: `free` pour le mode gratuit strict
- Health check HTTP:
  - path: `/up`
  - port: `8000`

Commande de demarrage:

```sh
start-web
```

Ce script:

- genere la cle si besoin
- vide les anciens caches Laravel
- tente `storage:link`
- lance les migrations
- cree ou met a jour le compte admin si `ADMIN_EMAIL` et `ADMIN_PASSWORD` sont definis
- met en cache config/routes/views
- demarre Laravel sur `0.0.0.0:${PORT:-8000}`

## 5. Service Reverb

Ignorez cette section en mode gratuit strict.

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

### Mode gratuit strict

1. Creez la base PostgreSQL Koyeb.
2. Creez le service web avec le `Dockerfile`.
3. Choisissez l'instance `free`.
4. Exposez le port `8000`.
5. Configurez le health check HTTP sur `/up`.
6. Copiez les variables de `.env.koyeb.free.example`.
7. Remplacez `APP_URL` et `GOOGLE_REDIRECT_URI` avec le domaine Koyeb du service web.
8. Redeployez.

### Mode complet avec Reverb

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
- `https://<web-service>.koyeb.app/healthz` doit confirmer la connexion base de donnees.
- La page d'accueil doit charger le CSS Tailwind et afficher le footer.
- Dans Google Cloud Console, l URI de redirection autorisee doit etre exactement `https://<web-service>.koyeb.app/auth/google/callback`.
- La connexion Google doit revenir vers le dashboard apres autorisation.
- `/admin/mentor-verifications` doit etre accessible avec le compte admin configure.
- Les nouveaux conseillers doivent apparaitre en `pending` jusqu'a validation admin.
- Les justificatifs de verification sont conserves en base de donnees pour survivre aux redeploiements.
- La page de messagerie doit se charger.
- En mode gratuit strict, deux navigateurs sur la meme conversation doivent recevoir les messages apres quelques secondes.
- En mode complet, deux navigateurs sur la meme conversation doivent recevoir les messages sans reload perceptible.
- Les appels audio/video doivent ouvrir la salle Jitsi.

Si le front ne se met pas a jour en ligne:

1. Verifiez que le service utilise bien ce `Dockerfile`.
2. Relancez un redeploiement complet du service web.
3. Controlez dans les logs que `npm run build` a produit `public/build/manifest.json`.
4. Controlez que `php artisan optimize:clear` puis `php artisan view:cache` passent au demarrage.

## 9. Limites du mode gratuit

Etat verifie dans la documentation Koyeb le 2026-05-18:

- Koyeb annonce une seule instance web `free` par organisation: `512MB RAM`, `0.1 vCPU`, `2GB SSD`.
- Cette instance peut descendre a zero apres 1 heure sans trafic.
- Koyeb annonce aussi une base PostgreSQL gratuite limitee a 5 heures actives/mois et 1GB de stockage.

Conseil pratique:

- pour rester gratuit: gardez seulement le service web et utilisez `BROADCAST_CONNECTION=log`
- pour un vrai temps reel WebSocket: ajoutez le service Reverb, ou utilisez Pusher/Ably plus tard

## 10. Prochaine etape utile

Prochaines etapes utiles:

- un `koyeb.yaml` si vous voulez piloter les services par configuration
- un durcissement production du service web (Caddy/Nginx + PHP-FPM)
- un mode "worker" separe si vous passez plus tard aux queues asynchrones
