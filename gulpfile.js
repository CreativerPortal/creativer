var gulp = require('gulp'),
    connect = require('gulp-connect'),
    opn = require('opn'),
    wiredep = require('wiredep').stream,
    useref = require('gulp-useref'),
    gulpif = require('gulp-if'),
    uglify = require('gulp-uglify'),
    minifyCss = require('gulp-minify-css');

gulp.task('connect', function() {
    connect.server({
        root: 'app_dev',
        livereload: true
    });
    opn("http://localhost:8080/");
});

gulp.task('html', function () {
    gulp.src('*.html')
        .pipe(connect.reload());
});

gulp.task('css', function () {
    gulp.src('*')
        .pipe(connect.reload());
});

gulp.task('dist', function () {
    var assets = useref.assets();

    return gulp.src('app_dev/*.html')
        .pipe(assets)
        .pipe(gulpif('*.js', uglify()))
        .pipe(gulpif('app_dev/css/pages/*.css', minifyCss()))
        .pipe(assets.restore())
        .pipe(useref())
        .pipe(gulp.dest('web'));
});

gulp.task('bower', function () {
    gulp.src('app_dev/index.html')
        .pipe(wiredep({
            directory: 'app_dev/vendor',
            goes: 'here'
        }))
        .pipe(gulp.dest('app_dev'));
});

gulp.task('watch', function () {
    gulp.watch(['app_dev/*'], ['html']);
    gulp.watch(['app_dev/css/pages/*'], ['css']);
    gulp.watch(['app_dev/css/*'], ['css']);
});

gulp.task('default', ['connect', 'watch']);

gulp.on("error", function(err){
    console.log(err);
});