var gulp = require('gulp');
var plugins = require('gulp-load-plugins')({camelize: true});
var browserSync = require('browser-sync').create();

// Styles
gulp.task('admin-styles', function() {
        return gulp.src('./admin/css/*.scss')
                .pipe(plugins.sass())
                .pipe(gulp.dest('./admin/css/build'))
                .pipe(plugins.minifyCss())
                .pipe(gulp.dest('./admin/css/min'))
                .pipe(plugins.notify({message: 'Admin style task complete'}))
                .pipe(plugins.filter('**/*.css')) // Filtering stream to only css files
        .pipe(browserSync.reload({stream:true}));
});

gulp.task('public-styles', function() {
        return gulp.src('./public/css/*.scss')
                .pipe(plugins.sass())
                .pipe(gulp.dest('./public/css/build'))
                .pipe(plugins.minifyCss())
                .pipe(gulp.dest('./public/css/min'))
                .pipe(plugins.notify({message: 'Public style task complete'}))
                .pipe(plugins.filter('**/*.css')) // Filtering stream to only css files
        .pipe(browserSync.reload({stream:true}));
});

// Watch
gulp.task('watch', function() {

        browserSync.init({
            proxy: "wordpress.lioncodestudio.com/441"
        });

        // Watch admin .scss files
        gulp.watch('./admin/css/*.scss', ['admin-styles']);

        // Watch public .scss files
        gulp.watch('./public/css/*.scss', ['public-styles']);

        // Watch .php files
        gulp.watch('*.php').on('change', browserSync.reload);

});

gulp.task('default', ['admin-styles', 'public-styles', 'watch']);