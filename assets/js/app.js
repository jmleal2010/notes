/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.css';
import 'bootstrap'
import 'bootstrap/js/src/modal'
import 'datatables.net'
import 'select2/dist/css/select2.min.css'
import 'select2/dist/js/select2.min.js'
import 'axios'
import 'moment'
import './main'

const $ = require('jquery');

global.$ = global.jQuery = $;


// start the Stimulus application
import '../bootstrap';
