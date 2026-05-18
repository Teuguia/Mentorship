# Deploiement Railway

Ce projet peut tourner sur Railway avec:

- 1 service web Laravel
- 1 service Reverb pour la messagerie temps reel
- 1 base PostgreSQL Railway

Le projet utilise deja un `Dockerfile`; Railway le detecte automatiquement quand le repo est connecte.

References officielles utiles:

- Dockerfile et lifecycle deploiement: https://docs.railway.com/deployments/reference
- Build / start commands: https://docs.railway.com/reference/build-and-start-commands
- Healthchecks: https://docs.railway.com/reference/healthchecks
- Variables et references `${{Service.VAR}}`: https://docs.railway.com/reference/variables
- PostgreSQL Railway: https://docs.railway.com/databases/postgresql/

## 1. Architecture conseillee

Dans un meme projet Railway, creez:

1. `Postgres`
2. `mentorconnect-web`
3. `mentorconnect-reverb`

Les deux services applicatifs pointent vers le meme repo GitHub et le meme `Dockerfile`.

## 2. Base PostgreSQL

Dans Railway:

1. Cliquez sur `+ New`.
2. Ajoutez une base `PostgreSQL`.
3. Gardez le nom de service `Postgres` ou adaptez les references de variables.

Railway expose notamment:

- `PGHOST`
- `PGPORT`
- `PGUSER`
- `PGPASSWORD`
- `PGDATABASE`
- `DATABASE_URL`

Le projet utilise les variables `DB_*`, referencees depuis `Postgres`.

## 3. Service web Laravel

Creez un service depuis le repo GitHub.

Parametres:

- Source: GitHub repo
- Builder: Dockerfile auto-detecte
- Start command: laissez vide ou utilisez `start-web`
- Public networking: active
- Healthcheck path: `/up`
- Healthcheck timeout: `300`

Variables a mettre sur `mentorconnect-web`:

```env
APP_NAME=MentorConnect
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=fr_FR
APP_KEY=base64:VOTRE_CLE_APP

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=info

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
DB_SSLMODE=require

SESSION_DRIVER=cookie
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

ADMIN_NAME=Administrateur
ADMIN_EMAIL=votre-email-admin@example.com
ADMIN_PASSWORD=UN_MOT_DE_PASSE_LONG

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=mentorconnect
REVERB_APP_KEY=VOTRE_CLE_REVERB
REVERB_APP_SECRET=VOTRE_SECRET_REVERB
REVERB_HOST=${{mentorconnect-reverb.RAILWAY_PUBLIC_DOMAIN}}
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

JITSI_BASE_URL=https://meet.jit.si

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Google OAuth est optionnel. Si vous l'activez, ajoutez:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://${{RAILWAY_PUBLIC_DOMAIN}}/auth/google/callback
```

## 4. Service Reverb

Creez un deuxieme service depuis le meme repo GitHub.

Parametres:

- Source: meme repo GitHub
- Builder: Dockerfile auto-detecte
- Start command: `start-reverb`
- Public networking: active
- Pas de healthcheck HTTP obligatoire

Variables a mettre sur `mentorconnect-reverb`:

```env
APP_NAME=MentorConnect
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:LA_MEME_CLE_APP_QUE_LE_WEB

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
DB_SSLMODE=require

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=mentorconnect
REVERB_APP_KEY=LA_MEME_CLE_REVERB_QUE_LE_WEB
REVERB_APP_SECRET=LE_MEME_SECRET_REVERB_QUE_LE_WEB
REVERB_HOST=${{RAILWAY_PUBLIC_DOMAIN}}
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

Important:

- `REVERB_APP_KEY` et `REVERB_APP_SECRET` doivent etre identiques sur les deux services.
- Sur le service web, `REVERB_HOST` pointe vers le domaine public du service Reverb.
- Sur le service Reverb, `REVERB_HOST` pointe vers son propre domaine public Railway.

## 5. Valeurs a generer

Pour `APP_KEY`:

```sh
php artisan key:generate --show
```

Pour Reverb, utilisez deux valeurs longues aleatoires:

- `REVERB_APP_KEY`
- `REVERB_APP_SECRET`

Ne commitez jamais ces secrets.

## 6. Verification

Apres deploiement du service web:

```text
https://<web-domain>/up
https://<web-domain>/healthz
https://<web-domain>/login
```

`/up` verifie que Laravel repond.

`/healthz` verifie:

- l'app Laravel
- la connexion PostgreSQL
- les tables principales
- la presence de la config Reverb

Pour la messagerie:

1. Connectez deux comptes dans deux navigateurs.
2. Ouvrez la meme conversation.
3. Envoyez un message.
4. Le message doit apparaitre sans rechargement si Reverb est OK.
5. Si Reverb n'est pas encore pret, le polling le recuperera apres quelques secondes.

Pour les appels:

1. Ouvrez une conversation.
2. Cliquez `Appel audio` ou `Appel video`.
3. La salle Jitsi doit s'ouvrir dans l'interface.
4. Si le navigateur bloque l'iframe, utilisez le bouton `Ouvrir l'appel`.

## 7. Depannage rapide

Si le build echoue:

- verifiez que Railway utilise bien le `Dockerfile`
- regardez les logs `composer install`
- regardez les logs `npm run build`

Si le service demarre puis tombe:

- regardez les logs qui commencent par `[start-web]`
- verifiez `APP_KEY`
- verifiez les references `Postgres.PGHOST`, `Postgres.PGPORT`, etc.
- testez `/healthz`

Si la messagerie temps reel ne marche pas:

- verifiez que `mentorconnect-reverb` est public
- verifiez que `REVERB_HOST` du web pointe vers le domaine public Reverb
- verifiez que les secrets Reverb sont identiques entre web et Reverb

Si seulement l'envoi de messages marche:

- c'est probablement Reverb qui n'est pas connecte
- le polling reste disponible comme secours
