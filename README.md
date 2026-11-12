# Laravel POS TFA7

**Student:** Lanz Wayne Villaruel  
**Course:** IT0049 Web System Technologies  
**Activity:** Forms Validation and File Upload

This project extends the supplied Laravel POS TFA6 application with customer
and user creation, editing, server-side validation, and prepared user avatars.
The existing Laravel 12 framework is retained so the extension works with the
starter project.

## Implemented requirements

| Requirement | Implementation |
| --- | --- |
| Create customer | `GET /customers/create` and `POST /customers` |
| Customer validation | Full name required, valid email required, optional phone |
| Create user | `GET /users/create` and `POST /users` |
| User validation | Unique username required and full name required |
| Edit customer | `GET /customers/{customer}/edit`, then `PUT/PATCH /customers/{customer}` |
| Edit user | `GET /users/{user}/edit`, then `PUT/PATCH /users/{user}` |
| Error redisplay | Error summary, field errors, and Blade `old()` values |
| Avatar migration | Nullable `users.avatar` column in a separate migration |
| File validation | Image content and MIME validation, maximum 2048 KB |
| Prepared image | Intervention Image v3 with GD, center crop to 256 by 256 PNG |
| Public storage | `Storage::disk('public')->put(...)` and relative path in database |
| Listing avatar | Prepared image or bundled local placeholder |
| Database reproduction | All migrations and the five-customer five-user seeder included |

Avatar uploads accept JPG, PNG, GIF, and WebP, with an additional 4096 by 4096
pixel limit. GIFs are saved as a still thumbnail. Avatar replacement removes
the old file after the record saves. Editing without a file preserves the
current avatar. If saving the record fails, the new file is removed and the
previous image is kept. No original upload is retained.

**Image package:** Intervention Image v3. If your class requires a particular
instructor-approved package, confirm that Intervention Image is accepted.
The assignment did not name the approved package.

## Requirements

- PHP 8.2 or a compatible newer version
- Composer 2
- PHP extensions: GD, Fileinfo, Mbstring, PDO SQLite, DOM, and XML
- Exif recommended for photo orientation
- No Node or frontend build is needed for these Blade pages

For XAMPP, enable the needed extensions in the `php.ini` used by your CLI
(`php --ini`). Restart Apache after changing its PHP configuration.
Set `upload_max_filesize=3M` and `post_max_size=4M` so Laravel can return its
own 2 MB validation message for files slightly over the activity limit.

## Local setup on Windows PowerShell

Extract the ZIP and open a terminal inside this folder.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate

if (!(Test-Path database/database.sqlite)) {
    New-Item -ItemType File -Path database/database.sqlite
}

php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open [Customers](http://127.0.0.1:8000/customers) or
[User Accounts](http://127.0.0.1:8000/users).

The example environment uses SQLite and `APP_URL=http://127.0.0.1:8000`.
If you use another host or port, update `APP_URL` and run
`php artisan config:clear`.

If an environment file already exists, keep it and update the needed settings
instead of copying over it. Do not generate a new production application key
on every restart.

**Dependency lock:** The old TFA6 lock did not include Intervention Image.
A stale lock is not included in this completed source ZIP. The first
`composer install` resolves the dependencies and creates `composer.lock`.
Commit that generated lock before deploying so subsequent installs use the
same versions.

### Updating an existing TFA6 installation

Back up your database, environment file, and uploaded files. Apply the source
changes, including removing the old `composer.lock` if you copied files over
an existing folder, then run:

```powershell
composer install
php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan test
```

Do not run `migrate:fresh` on an existing database: it deletes the records.
The avatar migration adds a nullable column without clearing existing users.

## Test the application

```powershell
php artisan test
php artisan route:list --path=customers
php artisan route:list --path=users
```

The 21 feature tests cover both forms, required fields, email validation,
duplicate usernames, input redisplay, prefilled edits, updates, missing
records, image preparation, avatar replacement, and the 2 MB size boundary.
A GitHub Actions workflow runs the suite after a push.

**Verification status:** This workspace had no PHP, Composer, or Docker
runtime. The feature tests and application have not been executed here.
Run the commands above and complete the browser checks below before submission.

### Browser checks before submission

1. Open Customers, select Create customer, and save a valid name and email.
2. Submit a malformed email and check that the error and previous input appear.
   Browser email validation may block submission first; disable native
   validation temporarily in developer tools to inspect server validation.
3. Edit the new customer, save, and check the listing.
4. Create a user, then try the same username again and check the error.
5. Edit the user while keeping its username; the save should succeed.
6. Upload a JPG, PNG, GIF, or WebP of at most 2 MB from the edit form.
7. Check that the listing shows the thumbnail and the database stores only
   `avatars/<uuid>.png`.
8. Upload a text file renamed to JPG and an image over 2 MB; both should fail.
9. Edit without selecting a file; the current picture should remain.
10. Replace the picture; the listing should show the replacement.
11. Check a user with no picture; the local placeholder should display.

## GitHub submission

This download contains raw source, migrations, seeders, and tests. To update
your existing repository, copy the project files into its local checkout,
including the hidden `.github` folder and `.env.example`. Do not copy your
local `.env`, `vendor`, generated database, or uploaded avatar files.

Run setup and tests, commit the generated `composer.lock`, then commit and
push the changed source through GitHub Desktop or Git. Submit the URL of the
repository you actually pushed. Do not use this ZIP download URL as the
repository link.

## Hosting on Render

The included Dockerfile installs GD and the required PHP extensions.
It serves Laravel's `public/` directory on port 10000, runs migrations at
startup, and creates the public storage link.

1. Push the tested source to GitHub, including the generated dependency lock.
2. In Render, create a Docker web service or update your existing service to
   build the updated repository and branch.
3. Set these environment variables:

| Variable | Value |
| --- | --- |
| APP_KEY | Output of `php artisan key:generate --show` from your local project |
| APP_URL | Your actual public HTTPS Render URL |
| APP_ENV | production |
| APP_DEBUG | false |

The container already sets SQLite, file sessions and cache, and a synchronous
queue. It seeds sample records only when its seed marker is absent.

Render's default filesystem is temporary. For durable SQLite records and
avatars, attach a persistent disk at `/var/www/html/persistent` and also set:

| Variable | Value |
| --- | --- |
| DB_DATABASE | /var/www/html/persistent/database.sqlite |
| PUBLIC_STORAGE_PATH | /var/www/html/persistent/avatars |

Persistent disks require a hosting plan that supports them. Without a disk,
new records and uploaded images can be lost on restart or redeploy. A disk on
an existing service starts empty; restore a database backup into it if you
need the old data.

Deploy, open both directories, and repeat the create/edit/upload checks.
The old TFA6 URL in the starter README is not proof that TFA7 is deployed.
No new repository or hosted URL was created from this workspace.

### Final submission

Submit both actual links:

- GitHub repository containing the completed raw source.
- Hosted application verified to have the TFA7 forms and avatar upload.

This is an academic directory application. Authentication and sales
processing are outside the supplied activity.

## Code organization

- `routes/web.php` defines resource routes for index, create, store, edit, update.
- `app/Http/Requests` holds reusable server-side validation.
- `app/Http/Controllers` handles Eloquent reads and writes.
- `app/Services/AvatarStorage.php` prepares and stores thumbnails.
- `resources/views/customers` and `resources/views/users` contain Blade pages.
- `database/migrations` and `database/seeders` reproduce the database.
- `tests/Feature` contains the activity's behavior tests.

## References

- [Laravel 12 Validation](https://laravel.com/docs/12.x/validation)
- [Laravel 12 File Storage](https://laravel.com/docs/12.x/filesystem)
- [Intervention Image v3](https://image.intervention.io/v3)
- [Render Laravel Docker deployment](https://render.com/docs/deploy-php-laravel-docker)
- [Render persistent disks](https://render.com/docs/disks)

The handout links Laravel 13 documentation; this starter declares Laravel 12.
The implementation uses the APIs of its existing framework version.
