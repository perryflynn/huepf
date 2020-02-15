# huepf.net

Code of the shortlink service https://hüpf.net.

A file-based shortlink service with an very basic admin panel.

## Installation

- Put contents of `src/` on your server
- Make `src/su/links/` folder writable for the webserver user
- Make password protection of `src/su/` (.htaccess & .htpassws) working
    - **You need to create your own rules for password protection and url rewriting if you don't use Apache2 as webserver**
- Add imprint and data privacy links to `src/index.php`
- Profit
