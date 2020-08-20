/**
 * Created by chung Pham on 08/18/20.
 */
'use strict';

const folderPlugin = '';
const textdomainPlugin = 'opalestate-pro';
const path = require('path');

module.exports = function (grunt) {
    var deploy = {
        // pkg: grunt.file.readJSON('package.json'),
        addtextdomain: {
            plugin: {
                options: {
                    textdomain: textdomainPlugin,
                    updateDomains: true,
                },
                src: [path.join(folderPlugin, '**/*.php')]
            }
        },
        makepot: {
            plugin: {
                options: {
                    cwd: folderPlugin,
                    domainPath : 'languages',
                    potFilename: 'opalestate-pro.pot',
                    type       : 'wp-plugin',
                    processPot: function (pot, options) {
                        pot.headers['language-team'] = 'Themelexus Team <themelexus@gmail.com>';
                        return pot;
                    },
                }
            },
        }
    }

    grunt.initConfig(deploy);

    grunt.loadNpmTasks('grunt-wp-i18n');
};
