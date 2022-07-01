const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore.setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/app.js')
  .addEntry('chart.js', './assets/js/chart.js')

  // Vue entries
  .addEntry('editor', './assets/vue/editor/index.js')

  //.addEntry('page1', './assets/page1.js')
  //.addEntry('page2', './assets/page2.js')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())
  // enables Sass/SCSS support
  .enableSassLoader()
  .enablePostCssLoader()
  .enableVueLoader(() => {}, { runtimeCompilerBuild: false })
  // copies images without additional processing
  .copyFiles({
      from: './assets/images',
      to: 'images/[path][name].[ext]',
      pattern: /\.(png|jpg|jpeg|ico)$/
  })
;

module.exports = Encore.getWebpackConfig();
