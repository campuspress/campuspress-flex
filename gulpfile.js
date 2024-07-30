// Defining requirements
import gulp from 'gulp';
import plumber from 'gulp-plumber';

import dartSass from 'sass';
import gulpSass from 'gulp-sass';
const sass = gulpSass(dartSass);

import babel from 'gulp-babel';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify';
import imagemin, { gifsicle, mozjpeg, optipng, svgo } from 'gulp-imagemin';
import sourcemaps from 'gulp-sourcemaps';

import sync from 'browser-sync';
const browserSync = sync.create();

import del from 'del';
import cleanCSS from 'gulp-clean-css';
import replace from 'gulp-replace';
import autoprefixer from 'autoprefixer';

// Configuration file to keep your code DRY
import cfg from './gulpconfig.json' with { type: "json" };
var paths = cfg.paths;

/**
 * Compiles .scss to .css files.
 *
 * Run: gulp sass
 */
gulp.task( 'sass', function() {
	return gulp
		.src( paths.sass + '/*.scss' )
		.pipe(
			plumber( {
				errorHandler( err ) {
					console.log( err );
					this.emit( 'end' );
				},
			} )
		)
		.pipe( sourcemaps.init( { loadMaps: true } ) )
		.pipe( sass( { errLogToConsole: true } ) )
		.pipe( postcss( [ autoprefixer() ] ) )
		.pipe( sourcemaps.write( undefined, { sourceRoot: null } ) )
		.pipe( gulp.dest( paths.css ) );
} );

/**
 * Optimizes images and copies images from src to dest.
 *
 * Run: gulp imagemin
 */
gulp.task( 'imagemin', () =>
	gulp
		.src( paths.imgsrc + '/**' )
		.pipe(
			imagemin(
				[
					// Bundled plugins
					gifsicle( {
						interlaced: true,
						optimizationLevel: 3,
					} ),
					mozjpeg( {
						quality: 100,
						progressive: true,
					} ),
					optipng(),
					svgo(),
				],
				{
					verbose: true,
				}
			)
		)
		.pipe( gulp.dest( paths.img ) )
);

/**
 * Minifies css files.
 *
 * Run: gulp minifycss
 */
gulp.task('minifycss', function () {
	return gulp
		.src([
			paths.css + '/block-editor.css',
			paths.css + '/theme.css',
		])
		.pipe(
			sourcemaps.init( {
				loadMaps: true,
			} )
		)
		.pipe(
			cleanCSS( {
				compatibility: '*',
			} )
		)
		.pipe(
			plumber( {
				errorHandler( err ) {
					console.log( err );
					this.emit( 'end' );
				},
			} )
		)
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest( paths.css ) );
} );

/**
 * Delete minified CSS files and their maps
 *
 * Run: gulp cleancss
 */
gulp.task( 'cleancss', function() {
	return del( paths.css + '/*.min.css*' );
} );

/**
 * Compiles .scss to .css minifies css files.
 *
 * Run: gulp styles
 */
gulp.task( 'styles', function( callback ) {
	gulp.series( 'sass', 'minifycss' )( callback );
} );

/**
 * Watches .scss, .js and image files for changes.
 * On change re-runs corresponding build task.
 * 
 * Run: gulp watch
 */
gulp.task( 'watch', function() {
	gulp.watch(
		[ paths.sass + '/**/*.scss', paths.sass + '/*.scss' ],
		gulp.series( 'styles' )
	);
	gulp.watch(
		[
			paths.dev + '/js/**/*.js',
			'js/**/*.js',
			'!js/theme.js',
			'!js/theme.min.js',
		],
		gulp.series( 'scripts' )
	);

	// Inside the watch task.
	gulp.watch( paths.imgsrc + '/**', gulp.series( 'imagemin-watch' ) );
} );

/**
 * Starts browser-sync task for starting the server.
 *
 * Run: gulp browser-sync
 */
gulp.task( 'browser-sync', function () {
	browserSync.init(cfg.browserSyncWatchFiles, cfg.browserSyncOptions);
} );

/**
 * Ensures the 'imagemin' task is complete before reloading browsers
 */
gulp.task(
	'imagemin-watch',
	gulp.series('imagemin', function () {
		browserSync.reload();
	})
);

/**
 * Starts watcher with browser-sync.
 * Browser-sync reloads page automatically on your browser.
 * 
 * Run: gulp watch-bs
 */
gulp.task('watch-bs', gulp.parallel('browser-sync', 'watch'));

// Run:
// gulp scripts.
// Uglifies and concat all JS files into one
gulp.task('scripts', function () {
	var scripts = [
		// Start - All BS4 stuff
		paths.dev + '/js/bootstrap4/bootstrap.bundle.js',
		paths.dev + '/js/themejs/*.js',

		// End - All BS4 stuff

		// Adding currently empty javascript file to add on for your own themes´ customizations
		// Please add any customizations to this .js file only!
		paths.dev + '/js/custom-javascript.js',
	];
	gulp
		.src(scripts, { allowEmpty: true })
		.pipe(babel({ presets: ['@babel/preset-env'] }))
		.pipe(concat('theme.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(paths.js));

	return gulp
		.src(scripts, { allowEmpty: true })
		.pipe(babel())
		.pipe(concat('theme.js'))
		.pipe(gulp.dest(paths.js));
});

// Deleting any file inside the /src folder
gulp.task('clean-source', function () {
	return del(['src/**/*']);
});

// Run:
// gulp copy-assets.
// Copy all needed dependency assets files from node_modules to theme's /js, /scss and /fonts folder. Run this task after npm update

////////////////// All Bootstrap SASS  Assets /////////////////////////
gulp.task('copy-assets', function (done) {
	////////////////// All Bootstrap 4 Assets /////////////////////////
	// Copy all JS files
	var stream = gulp
		.src( paths.node + '/bootstrap/dist/js/**/*.js' )
		.pipe( gulp.dest( paths.dev + '/js/bootstrap4' ) );

	// Copy all Bootstrap SCSS files
	gulp
		.src( paths.node + '/bootstrap/scss/**/*.scss' )
		.pipe( gulp.dest( paths.dev + '/sass/bootstrap4' ) );

	////////////////// End Bootstrap 4 Assets /////////////////////////

	// AOS SCSS files into /src/sass
	gulp
		.src(`${paths.node}/aos/dist/aos.css`)
		.pipe(gulp.dest(`${paths.css}`));

	// AOS JS files into /src/js
	gulp
		.src(`${paths.node}/aos/dist/aos.js`)
		.pipe(gulp.dest(`${paths.js}`));

	done();
});

// Deleting the files distributed by the copy-assets task
gulp.task('clean-vendor-assets', function () {
	return del([
		paths.dev + '/js/bootstrap4',
		paths.dev + '/sass/bootstrap4',
		`${paths.js}/**/popper.min.js`,
		`${paths.js}/**/popper.js`,
		paths.vendor !== '' ? paths.js + paths.vendor + '/**' : ''
	]);
});

/**
 * Deletes all files inside the dist folder and the folder itself.
 *
 * Run: gulp clean-dist
 */
gulp.task('clean-dist', function () {
	return del( paths.dist );
});

// Run
// gulp dist
// Copies the files to the dist folder for distribution as simple theme
gulp.task(
	'dist',
	gulp.series(['clean-dist'], function () {
		return gulp
			.src(
				[
					'**/*',
					'!' + paths.node,
					'!' + paths.node + '/**',
					'!' + paths.dev,
					'!' + paths.dev + '/**',
					'!' + paths.dist,
					'!' + paths.dist + '/**',
					'!' + paths.distprod,
					'!' + paths.distprod + '/**',
					'!' + paths.sass,
					'!' + paths.sass + '/**',
					'!' + paths.composer,
					'!' + paths.composer + '/**',
					'!+(readme|README).+(txt|md)',
					'!*.+(dist|json|js|lock|xml)',
					'!CHANGELOG.md',
				],
				{ buffer: true }
			)
			.pipe(
				replace(
					'/js/jquery.slim.min.js',
					'/js' + paths.vendor + '/jquery.slim.min.js',
					{ skipBinary: true }
				)
			)
			.pipe(
				replace('/js/popper.min.js', '/js' + paths.vendor + '/popper.min.js', {
					skipBinary: true
				})
			)
			.pipe(gulp.dest(paths.dist));
	})
);

/**
 * Deletes all files inside the dist-product folder and the folder itself.
 *
 * Run: gulp clean-dist-product
 */
gulp.task('clean-dist-product', function () {
	return del( paths.distprod );
});

// Run
// gulp dist-product
// Copies the files to the /dist-prod folder for distribution as theme with all assets
gulp.task(
	'dist-product',
	gulp.series(['clean-dist-product'], function () {
		return gulp
			.src([
				'**/*',
				'!' + paths.node,
				'!' + paths.node + '/**',
				'!' + paths.composer,
				'!' + paths.composer + '/**',
				'!' + paths.dist,
				'!' + paths.dist + '/**',
				'!' + paths.distprod,
				'!' + paths.distprod + '/**',
			])
			.pipe(gulp.dest(paths.distprod));
	})
);

// Run
// gulp compile
// Compiles the styles and scripts and runs the dist task
gulp.task('compile', gulp.series('styles', 'scripts', 'dist'));

// Run:
// gulp
// Starts watcher (default task)
gulp.task( 'default', gulp.series( 'watch' ) );