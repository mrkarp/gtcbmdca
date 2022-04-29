const { src, dest } = require('gulp');
const sass = require('gulp-sass');
const minifyCSS = require('gulp-csso');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const argv = require('yargs').argv;
var sourcemaps = require('gulp-sourcemaps');

let publish = (argv.publish == undefined) ? false : true;

function cssWithMaps() {
  return src('./sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(minifyCSS())
    .pipe(sourcemaps.write('./'))
    .pipe(dest('./'));
}

function js() {
  return src([
    './node_modules/jquery/dist/jquery.js',
    './node_modules/bootstrap/dist/js/bootstrap.bundle.js',
    './js/blank.js'],{ sourcemaps: !publish })
    .pipe(babel({presets: ['@babel/env']}))
    .pipe(concat('main.js'))
    .pipe(dest('.', { sourcemaps: !publish }));
}


async function build() {
  js();
  cssWithMaps();
}

exports.cssWithMaps = cssWithMaps;
exports.js = js;
exports.build = build;