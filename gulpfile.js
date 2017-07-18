'use strict';

const gulp = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const cleanCSS = require('gulp-clean-css');

// Build app.js
gulp.task('scripts', function () {
    return gulp.src(['html/easy_themes_src.js'])
        .pipe(uglify())
        .pipe(rename('easy_themes.js'))
        .pipe(gulp.dest('html'));
});

// Build bundle.css
gulp.task('styles', function () {
    return gulp.src(['html/easy_themes_src.css'])
        .pipe(cleanCSS({restructuring: false}))
        .pipe(rename('easy_themes.css'))
        .pipe(gulp.dest('html'));
});

// Watch task
gulp.task('watch', function () {
    gulp.watch(['html/easy_themes_src.js'], ['scripts']);
    gulp.watch(['html/easy_themes_src.css'], ['styles']);
});

// Build by default
gulp.task('default', ['build']);

// Build task
gulp.task('build', ['scripts', 'styles']);

// Build and watch task
gulp.task('build:watch', ['build', 'watch']);
