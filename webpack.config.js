var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    // the public path you will use in Symfony's asset() function - e.g. asset('build/some_file.js')
    .setManifestKeyPrefix('build/')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // the following line enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/admin', './assets/js/admin.js')

    .addStyleEntry('css/app', './assets/css/app.scss')
    .addStyleEntry('css/global', './assets/css/global.scss')
    .addStyleEntry('css/globalAdd', './assets/css/globalAdd.less')

    .addStyleEntry('css/admin', './assets/css/admin.scss')
    .addStyleEntry('css/globalAdmin', './assets/css/globalAdmin.scss')
    .addStyleEntry('css/globalAddAdmin', './assets/css/globalAddAdmin.less')
    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
    .enableLessLoader()
    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
