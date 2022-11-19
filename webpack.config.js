const Encore = require('@symfony/webpack-encore');
//let ContextReplacementPlugin = require('webpack/lib/ContextReplacementPlugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

let moduleDir = __dirname + '/node_modules/';
let websiteSrcDir = './assets/frontend/';

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('admin', './assets/js/admin.js')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    .addStyleEntry('style', [
        moduleDir + 'flatpickr/dist/flatpickr.min.css',
        websiteSrcDir + 'scss/styles.scss'
    ])

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'entry';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    .enableSassLoader(function(options) {
        options.sourceMap = true;
        options.sassOptions = {
            outputStyle: 'compressed',
            sourceComments: !Encore.isProduction(),
            // add or set custom options
            includePaths: [
                moduleDir + "bootstrap/assets/stylesheets",
                moduleDir + "font-awesome/scss",
                moduleDir + "compass-mixins/lib",
                moduleDir
            ],
        };
    })

    // enables PostCssLoader
    .enablePostCssLoader(function (options) {
        options.postcssOptions = {
            // the directory where the postcss.config.js file is stored
            config: 'assets/config'
        };
    })
    // Only include moment localisation for de.js and en.js:
    //.addPlugin(new ContextReplacementPlugin(/moment[\/\\]locale$/, /de\.|en\./))
    .addExternals( {
        jquery: 'jQuery',
        // load moment as external resource
        // https://dnasir.com/2018/06/07/webpack-moment-and-cdns/
         'moment': 'window.moment',      // for the standard moment imports
         '../moment': 'window.moment'    // for moment imports by locale files
    })
    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()


    .copyFiles({
        from: moduleDir + 'font-awesome/fonts',

        // optional target path, relative to the output dir
        to: 'fonts/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;
// export the final configuration
let webpackConfig = Encore.getWebpackConfig();

webpackConfig.stats.errors = true;
webpackConfig.stats.errorDetails = true;

if (Encore.isProduction()) {
    webpackConfig.optimization.minimize = true;
}

module.exports = Encore.getWebpackConfig();
