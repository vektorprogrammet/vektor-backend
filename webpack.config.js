const Encore = require('@symfony/webpack-encore');

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
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: '../images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        // to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
        })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')

    // Move scss files to build/pages/name_of_file.css
    .addStyleEntry('pages/admin/home', './assets/styles/pages/admin/home.scss')
    .addStyleEntry('pages/about_us_page', './assets/styles/pages/about_us_page.scss')
    .addStyleEntry('pages/assign_co_interviewer', './assets/styles/pages/assign_co_interviewer.scss')
    .addStyleEntry('pages/assistants', './assets/styles/pages/assistants.scss')
    .addStyleEntry('pages/contact_page', './assets/styles/pages/contact_page.scss')
    .addStyleEntry('pages/existing_admission', './assets/styles/pages/existing_admission.scss')
    .addStyleEntry('pages/home', './assets/styles/pages/home.scss')
    .addStyleEntry('pages/members', './assets/styles/pages/members.scss')
    .addStyleEntry('pages/my_receipts', './assets/styles/pages/my_receipts.scss')
    .addStyleEntry('pages/parents', './assets/styles/pages/parents.scss')
    .addStyleEntry('pages/partners', './assets/styles/pages/partners.scss')
    .addStyleEntry('pages/popup_lower', './assets/styles/pages/popup_lower.scss')
    .addStyleEntry('pages/profile', './assets/styles/pages/profile.scss')
    .addStyleEntry('pages/profile_photo', './assets/styles/pages/profile_photo.scss')
    .addStyleEntry('pages/teacher', './assets/styles/pages/teacher.scss')
    .addStyleEntry('pages/team', './assets/styles/pages/team.scss')

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

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
