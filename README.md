# ThemeRevision for Kanboard
![Release](https://img.shields.io/github/v/release/greyaz/ThemeRevision?color=%2332ab3d&style=flat-square)
![License](https://img.shields.io/github/license/greyaz/ThemeRevision?color=%233860f4&style=flat-square)
![Kanboard Support Good](https://img.shields.io/static/v1?label=Kanboard&message=%E2%89%A51.2.22%20Good&color=green&style=flat-square)
![Kanboard Support OK](https://img.shields.io/static/v1?label=Kanboard&message=%E2%89%A51.2.8%20OK&color=%23f7c400&style=flat-square)

ThemeRevision is a task-first and high-quality theme for [Kanboard](https://github.com/kanboard/kanboard). It's also aimed at better mobile experiences, common plugins' compatibilities, and customization friendly.

## Screenshots
<img src="Screenshots/7.png" width="97%"><br>

<img src="Screenshots/2.png" width="19%"> <img src="Screenshots/4.png" width="19%"> <img src="Screenshots/3.png" width="19%"> <img src="Screenshots/5.png" width="19%"> <img src="Screenshots/6.png" width="19%"> <img src="Screenshots/8.png" width="19%"> <img src="Screenshots/9.png" width="19%"> <img src="Screenshots/12.png" width="19%"> <img src="Screenshots/10.png" width="5.9%"> <img src="Screenshots/11.png" width="5.9%">

## Features
#### Task-first
* ThemeRevision provides a high-quality, minimalist UI that helps you focus on your tasks.
* Provide support for syntax highlighting.
* More Search boxes. Display a search box in a drop-down menu automatically if items are more than 25.

#### Better mobile experiences
* Modern mobile application's interactive behaviour. [screenshot1](Screenshots/10.png) [screenshot2](Screenshots/11.png)
* Accessible navigation with keyboard support, visible focus states, and reduced-motion preferences.
* First-class right-to-left layout support for Persian, Arabic, Hebrew, and other RTL languages.

#### Common plugins' compatibilities
* Calendar / Gantt / Group_assign / MarkdownPlus / MetaMagic / Table View ...

#### Dark mode
* An individually controlled panel for non-administrative users
* Three modes provided: Light / Dark / Auto

#### Customization friendly
* Customizable display content in the header of a column and the footer of a task card.
* All colors are configurable in the settings panel. [screenshot](Screenshots/5.png)
* Support Google fonts by just typing a font name.
* Switchable icon packages, _Google Material_ (default) and _Font Awesome_.
* Structured CSS files, easy to locate elements.  
* Utilize "rem" as the global measuring unit.

#### Modern design system
* Responsive typography, spacing, surfaces, elevation, and focus tokens.
* Refined task cards, navigation, forms, tables, dropdowns, modals, and settings.
* Touch-friendly controls and mobile safe-area support.

## Installation
* Install from the Kanboard plugin manager directly.  
   > **or**  
   > `git clone https://github.com/greyaz/ThemeRevision.git` into `your_kanboard_root/plugins`.
* ***Your own logo?***  
ThemeRevision utilize the file `favicon.png` in `your_kanboard_root/assets/img` as the head logo, replace it if needed.

### Docker Compose bind mount

For a Git-based deployment, clone this repository beside `docker-compose.yml`:

```shell
cd /opt/kanboard
git clone https://github.com/greyaz/ThemeRevision.git ThemeRevision
```

Keep the named plugins volume and add a read-only bind mount for ThemeRevision:

```yaml
services:
  app:
    volumes:
      - data:/var/www/app/data
      - plugins:/var/www/app/plugins
      - ./ThemeRevision:/var/www/app/plugins/ThemeRevision:ro
      - certs:/etc/nginx/ssl
```

Apply the mount once with `docker compose up -d --force-recreate app`. Future releases can be deployed from `/opt/kanboard/ThemeRevision` with `git pull --ff-only`; static PHP, CSS, and JavaScript changes are visible through the bind mount without rebuilding the image. If PHP OPcache is configured to ignore file timestamps, restart the app afterward with `docker compose restart app`.

The read-only mount is recommended for production. To use ThemeRevision's development mode, remove `:ro`, recreate the app container, and ensure the container process can write to `Asset`.

## Upgrading
* ***I have customized CSS files***  
Back up those CSS files you've customized in the folder `Asset/dev` before upgrading.

   > **and (< 1.1.4)**
   > * ***I have customized configs in Kanboard's settings panel***  
   > Export and then reset your configs in the settings panel before upgrading
   > 
   > * ***I have the file `config.php` on my server***  
   > Rename the file to `config.php.bak` before upgrading

## Customization
#### Colors / Icons / Fonts
* Just go to the [settings panel](Screenshots/5.png): *`Settings -> ThemeRevision Settings`*   
   > **or (< 1.0.9)**  
   > Copy and move (**do not delete**) the file `config-default.php` to `config.php` in the plugin directory. Then edit the values according to the instructions in it.

#### More Styles
1. ***Make sure*** the folder `your_kanboard_root/plugins/ThemeRevision/Asset` is ***writable and executable***.
2. Switch "Mode" to "Development" in the [settings panel](Screenshots/5.png).   
   > **or (< 1.0.9)**  
   > Switch "Mode" in the config file according to the alternative method mentioned in the previous section.
3. Edit raw CSS files in the folder `Asset/dev`.

## Author
- greyaz
- License MIT
