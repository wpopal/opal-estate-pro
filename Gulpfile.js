const path        = require('path');
const fs          = require('fs-extra');
const gulp        = require('gulp');
const browserSync = require('browser-sync');
const sourcemaps  = require('gulp-sourcemaps');
const sass        = require('gulp-sass');
const rtlcss      = require('rtlcss');
const cssnano     = require('gulp-cssnano');
const glob        = require("glob");
const concat      = require('gulp-concat');
const uglify      = require('gulp-uglify');
const inject      = require('gulp-inject');
const addsrc      = require('gulp-add-src');

var paths = {
	sass:"./assets/sass",
	dev:"./assets/babel",
	js:'./assets/js',
    css:'./assets/css',
    "node": "./node_modules/",
    "bower": "./bower_components/",
    "distprod": "./dist-product",
    "dist":"/Applications/XAMPP/xamppfiles/htdocs/wordpress/svn/opal-widgets-for-opaljob/trunk"
}

var folderPlugin = './';

gulp.task( 'watch', [ 'admin-opaljob-frontend' ], function(){
    gulp.watch([
     //   path.join(folderPlugin, 'src/js/frontend/*.js'),
      //  path.join(folderPlugin, 'src/js/admin/*.js'),
        path.join(folderPlugin, 'assets/scss/*.scss'),
        path.join(folderPlugin, 'assets/scss/**/*.scss'),
          path.join(folderPlugin, 'assets/scss/**/**/*.scss')
    ], () => {
       // gulp.start('babel-admin-opaljob-frontend');
       //  gulp.start('babel-admin-opaljob-admin');
        gulp.start('opaljob-frontend');
    });
} );

gulp.task( 'opaljob-frontend', function() {
    return gulp.src(  path.join(folderPlugin, 'assets/scss/**.scss')  )
      //  .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
     //   .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(path.join(folderPlugin, 'assets/')));
} );


// Run: 
// gulp scripts. 
// Uglifies and concat all JS files into one
gulp.task( 'scripts', function() {
    var scripts = [

        // Start - All BS4 stuff
        //    paths.dev + '/js/bootstrap4/bootstrap.bundle.js',
        // End - All BS4 stuff
        paths.dev + '/js/skip-link-focus-fix.js',
        // Adding currently empty javascript file to add on for your own themes´ customizations
        // Please add any customizations to this .js file only!
        paths.dev + '/js/custom-javascript.js'
    ];

    gulp.src( scripts )
    .pipe( concat( 'theme.min.js' ) )
    .pipe( uglify() )
    .pipe( gulp.dest( paths.js ) );

    gulp.src( scripts )
    .pipe( concat( 'theme.js' ) )
    .pipe( gulp.dest( paths.js ) );
});

gulp.task('admin-opaljob-frontend', function () {
    /*return gulp.src([
        path.join(folderPlugin, 'src/js/before.js'),
        path.join(folderPlugin, 'src/js/frontend/*.js'),
        path.join(folderPlugin, 'src/js/after.js'),
    ])
      //  .pipe(sourcemaps.init())
        .pipe(concat('frontend.js'))
        .pipe(uglify())
      //  .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(path.join(folderPlugin, 'assets/js/')));*/
});


gulp.task('babel-admin-opaljob-admin', function () {
    return gulp.src([
        path.join(folderPlugin, 'src/js/before.js'),
        path.join(folderPlugin, 'src/js/admin/*.js'),
        path.join(folderPlugin, 'src/js/after.js'),
    ])
      //  .pipe(sourcemaps.init())
        .pipe(concat('admin.js'))
        .pipe(uglify())
      //  .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(path.join(folderPlugin, 'assets/js/')));
});


gulp.task('babel-admin-opaljob', function () {
 
});

// Deleting any file inside the /dist folder
gulp.task( 'clean-dist', function() {
    // return del( [paths.dist + '/**'] );
   });
 gulp.task( 'dist', ['clean-dist'], function() {
     return gulp.src( ['**/*', '!*.js', '!' + paths.bower, '!' + paths.bower + '/**', '!' + paths.node, '!' + paths.node + '/**', '!' + paths.dev, '!' + paths.dev + '/**', '!' + paths.dist, '!' + paths.dist + '/**', '!' + paths.distprod, '!' + paths.distprod + '/**', '!' + paths.sass, '!' + paths.sass + '/**', '!readme.md', '!package.json', '!package-lock.json', '!gulpfile.js', '!project.json', '!CHANGELOG.md', '!.travis.yml', '!jshintignore',  '!codesniffer.ruleset.xml',"!**/*.map",  '*'], { 'buffer': false } )
       .pipe( gulp.dest( paths.dist ) );
 });