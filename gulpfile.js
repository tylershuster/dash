var gulp = require('gulp'),
		sass = require('gulp-sass'),
		prefix = require('gulp-autoprefixer'),
		browserSync = require('browser-sync').create();

var sassRoot = 'assets/sass';

gulp.task('sass', function() {
	gulp.src('./assets/sass/**/*.scss')
		.pipe(sass.sync().on('error', sass.logError))
		.pipe(prefix("last 3 versions"))
		.pipe(gulp.dest('./'))
		.pipe(browserSync.stream());
});

gulp.task('sass:watch', function() {
	gulp.watch('./assets/sass/**/*.scss', ['sass']);
});

