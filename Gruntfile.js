module.exports = function(grunt) {
    'use strict';

    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        phplint: {
            options: {
                phpArgs: {
                    '-lf': null
                }
            },
            all: ['src/**/*.php']
        },

        setPHPConstant: {
            productionCore: {
                    constant    : 'CORE_PATH',
                    value       : '/../../../progress-laravel',
                    file        : 'dist/public/index.php'
            },
            productionPublic: {
                constant    : 'PUBLIC_PATH',
                value       : '/../../getprogress.com/api',
                file        : 'dist/bootstrap/paths.php'
            },
            productionEnv: {
                constant    : 'LARAVEL_ENV',
                value       : 'production',
                file        : 'dist/bootstrap/start.php'
            }
        },

        concurrent: {
            target: {
                tasks: ['php:watch', 'watch'],
                options: {
                    logConcurrentOutput: true
                }
            }
        },

        watch: {
            lint: {
                files: ['src/**/*.php'],
                tasks: ['phplint']
            },
            markup: {
                files: ['**/*.php'],
                options: {
                    livereload: 36000
                }
                // tasks: ['php:dev']
            }
        },

        php: {
            options: {
                port: 8000,
                keepalive: true,
                open: true,
                base: 'src/',
                hostname: 'localhost',
                bin: '/Applications/MAMP/bin/php/php5.4.4/bin/php',
                ini: '/Applications/MAMP/bin/php/php5.4.4/conf/php.ini'
            },
            watch: {
                options: {
                    livereload: 8000
                }
            }
        },

        copy: {
            dist: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: 'src',
                    dest: 'dist',
                    src: ['**/*']
                }]
            }
        },

        rsync: {
            options: {
                args: ['--verbose', '--rsync-path=~/bin/rsync'],
                exclude: ['.git*', '*.scss', 'node_modules'],
                recursive: true
            },
            laravelcore: {
                options: {
                    src: 'dist/',
                    exclude: ['public', 'files', 'vendor', 'app/storage'],
                    dest: 'cmcnamara87@160.153.56.168:/home/cmcnamara87/progress-laravel',
                    ssh: true,
                    rescursive: true,
                }
            },
            laravelpublic: {
                options: {
                    src: 'dist/public/',
                    dest: 'cmcnamara87@160.153.56.168:/home/cmcnamara87/www/getprogress.com/api',
                    ssh: true,
                    rescursive: true,
                }
            }
        },
    });

    // grunt.registerTask('phpwatch', ['php:watch', 'watch']);

    grunt.registerTask('default', ['setPHPConstant:development', 'watch:lint']);
    // grunt.registerTask('default', ['setPHPConstant:development', 'concurrent']);

    grunt.registerTask('deploy', [
        'copy:dist',
        'setPHPConstant:productionCore',
        'setPHPConstant:productionPublic',
        'setPHPConstant:productionEnv',
        'rsync:laravelcore',
        'rsync:laravelpublic',
    ]);

};
