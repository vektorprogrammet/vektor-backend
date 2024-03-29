var gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer'),
    imagemin = require('gulp-imagemin'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cssnano = require('cssnano'),
    sass = require('gulp-sass'),
    changed = require('gulp-changed'),
    babel = require('gulp-babel');

var path = {
    dist: 'public/',
    src: 'assets/',
};

async function stylesProd() {
  var dest = path.dist + 'css/';
  return gulp.src(path.src + 'scss/**/*.scss')
    .pipe(plumber())
    .pipe(changed(dest))
    .pipe(sass())
    .pipe(autoprefixer())
    .pipe(cssnano())
    .pipe(gulp.dest(dest))
}

async function scriptsProd () {
  var dest = path.dist + 'js/';
  return gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(uglify())
      .pipe(gulp.dest(dest))
}

async function imagesProd () {
  var dest = path.dist + 'images/';
  return gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(imagemin({
        progressive: false,
        interlaced: false,
        optimizationLevel: 1
      }))
      .pipe(gulp.dest(dest))
}

async function stylesDev () {
  var dest = path.dist + 'css/';
  return gulp.src(path.src + 'scss/**/*.scss')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(sass())
      .pipe(autoprefixer())
      .pipe(gulp.dest(dest))
}

async function scriptsDev () {
  var dest = path.dist + 'js/';
  return gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(gulp.dest(dest))
}

async function imagesDev () {
  var dest = path.dist + 'images/';
  return gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(gulp.dest(dest))
}

async function icons () {
  var r = gulp.src('node_modules/@fortawesome/fontawesome-free/webfonts/**.*')
      .pipe(gulp.dest('public/webfonts/'));
  return r && gulp.src(path.src + 'webfonts/**.*')
    .pipe(gulp.dest('public/webfonts/'));
}

async function files () {
  return gulp.src(path.src + 'files/*')
      .pipe(changed('public/files/'))
      .pipe(gulp.dest('public/files/'))
}

async function vendor () {

  var r = gulp.src('node_modules/dropzone/**/*')
      .pipe(gulp.dest('public/vendor/dropzone/'));

  r = r && gulp.src('node_modules/cropperjs/dist/*')
    .pipe(gulp.dest('public/vendor/cropperjs/'));

  r = r && gulp.src('node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(gulp.dest('public/js'));

  r = r && gulp.src('node_modules/jquery/dist/jquery.min.js')
    .pipe(gulp.dest('public/js'));

  return r && gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/moment/min/moment.min.js',
    'node_modules/@coreui/coreui/dist/js/coreui.bundle.js',
  ])
    .pipe(concat('vendor.js'))
    .pipe(gulp.dest(path.dist + 'js/'));
}


async function watch () {
    gulp.watch(path.src + 'scss/**/*.scss', stylesDev);
    gulp.watch(path.src + 'js/**/*.js', scriptsDev);
    gulp.watch(path.src + 'images/*', imagesDev);
}


gulp.task('build:prod', gulp.parallel([stylesProd, scriptsProd, imagesProd, files, icons, vendor]));
gulp.task('build:dev', gulp.parallel([stylesDev, scriptsDev, imagesDev, files, icons, vendor]));
gulp.task('default', gulp.series(['build:dev', watch]));