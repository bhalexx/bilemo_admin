# OpenMobileRooms

### Introduction
This project is the Bilemo administration application using [Bilemo REST API][1] as part of my 7th [OpenClassRooms](https://openclassrooms.com/) PHP/Symfony Developer project. This application is built with **Symfony 3.4**.

Read instructions below to fork this application.

### Prerequisites
- PHP >=5.5.9
- MySQL
- [Composer][2] to install Symfony 3.4 and project dependencies

### Dependencies
This project uses:
- [CsaGuzzleBundle][3] a PHP HTTP client that makes it easy to send HTTP requests to call Bilemo API

This dependency is included in composer.json.

This project also uses:
- [WebPack Encore][4] for assets management
- [bootstrap-sass][5] Bootstrap SASS library
- [sass-loader][6] to compile your SCSS files to CSS

Those dependencies are included in package.json

### Installation
First of all, follow [Bilemo API instructions][7] to add your application as a Bilemo admin application and get credentials you will need later:
```
client_id: {YourClientId}
client_secret: {YourClientSecret}
username: {YourApplicationName}
password: {YourPassword}
```
Once you got your credentials, you can go on:

1. Clone this repository on your local machine by using this command line in your folder `git clone https://github.com/bhalexx/bilemo_admin.git`.
2. Rename `app/config/parameters.yml.dist` in `app/config/parameters.yml`, edit database parameters with yours and fill parameters with the credentials you got from Bilemo API.
3. Edit API URI (e.g.: URI from your forked [API project][1]) from `config(_dev).yml` in `csa_guzzle` section (parameter `base_uri`).
5. In project folder open a new terminal window and execute command line `composer install`. 
6. Then execute command line `npm install` to install node modules for assets management.

**Your project is ready to be run!**

### Customization
Assets are located in `app\Resources\assets`, and minified and built by Encore in `web\build`. To add/edit or any other configuration customization, look at `webpack.config.js`!

You can modify the max number of mobiles by editing `mobile_limit` in `config.yml`.

### Documentation
This application project is as documented as possible, so you can find:
- some [diagrams][8] to explain how the application communicates with the API
- [API documentation][9]

### Related projects
Two other projects were created to complete this 7th project:
- [Bilemo][10] - the Bilemo REST API
- [OpenMobileRooms][11] - a Bilemo B2B partner application

[1]: https://github.com/bhalexx/bilemo
[2]: https://getcomposer.org/
[3]: https://github.com/csarrazi/CsaGuzzleBundle
[4]: https://github.com/symfony/webpack-encore
[5]: https://github.com/twbs/bootstrap-sass
[6]: https://github.com/webpack-contrib/sass-loader
[7]: https://github.com/bhalexx/bilemo#authentication-to-access-api
[8]: https://github.com/bhalexx/bilemo_admin/tree/master/diagrams
[9]: https://github.com/bhalexx/bilemo#documentation
[10]: https://github.com/bhalexx/bilemo
[11]: https://github.com/bhalexx/openmobilerooms