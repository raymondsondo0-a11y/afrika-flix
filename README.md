# AfrikaFlix

Clean PHP/MySQL movie-library project for InfinityFree.

## Important security rules

- Never commit `private/config.php`.
- Never put database passwords, API keys, FTP passwords, or other secrets in GitHub.
- Only upload movies and media that you own or are legally allowed to distribute.
- After the first successful installation, remove `setup.php` from the server and from this repository.

## First deployment

1. Create the MySQL database in InfinityFree.
2. Add GitHub Actions secrets named `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, and `FTP_SERVER_DIR`.
3. Push to `main` or manually run the Deploy workflow.
4. Open `https://shizer.kesug.com/setup.php`.
5. Enter the InfinityFree database credentials and create the admin account.
6. Test the homepage and `/login.php`.
7. Delete `setup.php` from the server and repository.
8. Add movies from `/admin/movies.php`.

The application stores database credentials in `private/config.php`, which is ignored by Git and blocked from direct web access.
