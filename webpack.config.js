var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

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

    // enables @babel/preset-env polyfills
    .configureBabel((babelConfig) => {
        babelConfig.plugins.push("@babel/plugin-proposal-class-properties");
        babelConfig.plugins.push("@babel/plugin-proposal-nullish-coalescing-operator");
    }, {
        useBuiltIns: 'entry',
        corejs: 3,
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // enables PostCssLoader
    .enablePostCssLoader(function (options) {
        options.config = {
            // the directory where the postcss.config.js file is stored
            path: 'assets/config'
        };
    })

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // .copyFiles([
    //     {from: './node_modules/ckeditor/', to: 'third-party/ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
    //     {from: './node_modules/ckeditor/adapters', to: 'third-party/ckeditor/adapters/[path][name].[ext]'},
    //     {from: './node_modules/ckeditor/lang', to: 'third-party/ckeditor/lang/[path][name].[ext]'},
    //     {from: './node_modules/ckeditor/plugins', to: 'third-party/ckeditor/plugins/[path][name].[ext]'},
    //     {from: './node_modules/ckeditor/skins', to: 'third-party/ckeditor/skins/[path][name].[ext]'}
    // ])

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
