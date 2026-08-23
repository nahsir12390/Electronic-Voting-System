# Deploying To Render With Docker

This project is prepared for Render as a Docker web service.

## Why Docker is required

Render does not provide a native PHP runtime for this project setup, so the app must run inside a container. The included Dockerfile builds:

- PHP dependencies with Composer
- frontend assets with Vite
- a production runtime that starts Laravel on Render's assigned port

## Files added for deployment

- `Dockerfile`
- `.dockerignore`
- `docker/start.sh`
- `render.yaml`

## Important deployment assumptions

- This deployment uses SQLite as a file-based database.
- On Render, SQLite is only persistent if the database file is stored on a persistent disk.
- Render persistent disks are available only on paid web services.
- This avoids paying for a managed Postgres database, but it does not keep the deployment fully free on Render.
- `SESSION_DRIVER` and `CACHE_STORE` are configured to use the database.
- `QUEUE_CONNECTION` is set to `sync` so the app can run as a single web service without a separate worker.

## Deploy with Blueprint

1. Push this project to GitHub.
2. In Render, create a new Blueprint instance from the repository.
3. Render will detect `render.yaml` and propose:
   - one Docker web service
   - one persistent disk mounted at `/var/data`
4. Before the first deploy completes, set these required values in Render:
   - `APP_URL`

`APP_KEY` is generated automatically by the Blueprint.

## Required environment values

### APP_URL

Set this to your final Render public URL, for example:

```text
https://electronic-voting-system.onrender.com
```

## What happens on startup

The container startup script does the following:

1. clears old Laravel caches
2. creates the SQLite file if it does not exist
3. runs `php artisan migrate --force`
4. caches config, routes, and views
5. starts the application on Render's `PORT`

## Manual Render setup instead of Blueprint

If you do not want to use `render.yaml`, create these manually:

1. A new Web Service
   - Runtime: Docker
   - Root directory: project root
   - Plan: `starter` or higher
   - Add a persistent disk mounted at `/var/data`
2. These environment variables on the web service:

```text
APP_NAME=ElectraVote
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-service.onrender.com
APP_KEY=base64:your-generated-key
LOG_CHANNEL=stderr
LOG_LEVEL=info
DB_CONNECTION=sqlite
DB_DATABASE=/var/data/database.sqlite
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
SESSION_SECURE_COOKIE=true
APP_MAINTENANCE_DRIVER=file
```

If you use the Blueprint, Render can generate `APP_KEY` for you automatically.

## After deployment

Check these endpoints and flows:

1. `/up` returns healthy
2. `/` loads the landing page
3. login works
4. admin can create elections and candidates
5. voting writes to the SQLite file on the persistent disk

## Notes

- Route caching now works because the home page uses a controller instead of a route closure.
- If you later add queued jobs, create a separate Render worker service and switch `QUEUE_CONNECTION` from `sync` to `database` or `redis`.
- If you need a completely free host with persistent SQLite, Render is the wrong target because persistent disks require a paid web service.