# Deploiement Render

Ce chemin sert a remettre l'application en ligne sans Koyeb.

Le blueprint `render.yaml` cree:

- 1 base PostgreSQL Render gratuite
- 1 service web Laravel gratuit

Il ne cree pas de service Reverb. La messagerie reste utilisable grace au polling toutes les 4 secondes.

## 1. Important

Render indique actuellement:

- les services web gratuits s'endorment apres 15 minutes sans trafic
- chaque workspace a 750 heures gratuites par mois
- la base PostgreSQL gratuite expire 30 jours apres creation

Ce plan est donc bon pour tester ou relancer vite le projet. Pour garder les donnees durablement, il faudra plus tard passer la base PostgreSQL en plan payant ou migrer vers une autre base durable.

## 2. Avant Render

Envoyez le code sur GitHub:

```sh
git add .
git commit -m "Prepare Render free deployment"
git push origin main
```

## 3. Creer le projet

1. Ouvrez https://dashboard.render.com/
2. Connectez-vous avec GitHub.
3. Cliquez `New +`.
4. Choisissez `Blueprint`.
5. Selectionnez le repo `Teuguia/Mentorship`.
6. Render detecte `render.yaml`.
7. Lancez la creation.

## 4. Variables a renseigner

Render demandera les variables marquees `sync: false`:

- `APP_KEY`
- `APP_URL`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

Pour generer `APP_KEY`:

```sh
php artisan key:generate --show
```

Apres la premiere creation, mettez `APP_URL` avec le domaine public Render du service web, par exemple:

```env
APP_URL=https://mentorconnect-web.onrender.com
```

Si Google OAuth est active, ajoutez aussi:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://mentorconnect-web.onrender.com/auth/google/callback
```

## 5. Verification

Apres deploiement:

- `https://<votre-domaine-render>/up`
- `https://<votre-domaine-render>/healthz`
- `https://<votre-domaine-render>/login`
- `/admin/mentor-verifications` avec le compte admin

Si `/healthz` renvoie une erreur DB, verifiez les variables PostgreSQL generees par Render.
