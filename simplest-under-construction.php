<?php
/*
Plugin Name: Simplest Under Construction
Plugin URI: https://wordpress.org/plugins/custom-codes/
Description: This is the simplest plugin to restrict a Wordpress site to the public. You can allow them by IP or user role.
Author: Bilal TAS
Author URI: https://www.bilaltas.net
License: MIT
License URI: https://opensource.org/licenses/MIT
Version: 0.5

Copyright (c) 2018 Simplest Under Construction

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'SUC_FILE', __FILE__ );
define( 'SUC_DEBUG', false );


// Front-End
if ( !is_admin() )
	require_once( dirname( SUC_FILE ).'/suc_files/under_construction_public.php' );


// Back-End
else
	require_once( dirname( SUC_FILE ).'/suc_files/under_construction.php' );


// For Both
require_once( dirname( SUC_FILE ).'/suc_files/under_construction_admin-public_functions.php' );