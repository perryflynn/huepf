# huepf.net

Code of the shortlink service https://hüpf.net.

A file-based shortlink service with an very basic admin panel.

## Features

- File Based
- Very Basic Admin Panel in `src/su/`
- Removes referer

## Installation

- Put contents of `src/` on your server
- Make `src/su/links/` folder writable for the webserver user
- Make password protection of `src/su/` (.htaccess & .htpasswd) working
  - **You need to create your own rules for password protection and url rewriting if you don't use Apache2 as webserver**
- Create `src/su/config.json` file from `src/su/config.json.skel`
- Change settings in config.json
- Profit
