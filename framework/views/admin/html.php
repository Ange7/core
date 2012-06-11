<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

	$assets_minified = \Config::get('assets_minified', true);
	$paths = array(
		'jquery-nos' => 'static/novius-os/admin/novius-os/js/jquery.novius-os',
		'jquery-nos-appdesk' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.appdesk',
		'jquery-nos-listgrid' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.listgrid',
		'jquery-nos-loadspinner' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.loadspinner',
		'jquery-nos-preview' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.preview',
		'jquery-nos-thumbnailsgrid' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.thumbnailsgrid',
		'jquery-nos-treegrid' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.treegrid',
		'jquery-nos-ostabs' => 'static/novius-os/admin/novius-os/js/jquery.novius-os.ostabs',
		'log' => 'static/novius-os/admin/vendor/log',
		'jquery' => 'static/novius-os/admin/vendor/jquery/jquery-1.7.2.min',
		'jquery-validate' => 'static/novius-os/admin/vendor/jquery/jquery-validation/nos.validate',
		'jquery-form' => 'static/novius-os/admin/vendor/jquery/jquery-form/jquery.form.min',
		'jquery-ui' => 'static/novius-os/admin/vendor/jquery-ui/jquery-ui-1.8.21.custom.min',
		'wijmo-open' => 'static/novius-os/admin/vendor/wijmo/js/jquery.wijmo-open.all.2.1.0.min',
		'wijmo-complete' => 'static/novius-os/admin/vendor/wijmo/js/jquery.wijmo-complete.all.2.1.0.min',
		'tinymce' => 'static/novius-os/admin/vendor/tinymce/jquery.tinymce_src',
		'wysiwyg' => 'static/novius-os/admin/vendor/tinymce/jquery.wysiwyg',
		'link' => 'static/novius-os/admin/vendor/requirejs/link',
		'order' => 'static/novius-os/admin/vendor/requirejs/order.min',
	);

	if ($assets_minified) {
		$paths = array_merge($paths, array(
			'jquery-nos' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-appdesk' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-listgrid' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-loadspinner' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-preview' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-thumbnailsgrid' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-treegrid' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'jquery-nos-ostabs' => 'static/novius-os/admin/novius-os/js/novius-os.min',
			'tinymce' => 'static/novius-os/admin/vendor/tinymce/jquery.tinymce',
		));
	}
?>
<!DOCTYPE html>
<html>
<head>
<?php
	if (isset($base)) {
		echo '<base href="'.$base.'" />';
	}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?= $title ?></title>
<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="static/novius-os/admin/novius-os/img/noviusos.ico">
<link rel="stylesheet" href="static/novius-os/admin/vendor/wijmo/css/aristo/jquery-wijmo.css">
<link rel="stylesheet" href="static/novius-os/admin/vendor/wijmo/css/jquery.wijmo-complete.all.2.1.0.min.css">
<?php
	if ($assets_minified) {
?>
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/novius-os.min.css">
<?php
	} else {
?>
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/laGrid.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/novius-os.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.ostabs.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.appdesk.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.listgrid.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.treegrid.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.thumbnailsgrid.css">
<link rel="stylesheet" href="static/novius-os/admin/novius-os/css/jquery.novius-os.preview.css">
<?php
	}
?>
<?= $css ?>
<script type="text/javascript">
	var assets_minified = <?= \Format::forge($assets_minified)->to_json() ?>,
		require = {
			paths: <?= \Format::forge($paths)->to_json() ?>,
			jQuery: '1.7.2',
			catchError: true,
			priority: ['jquery'],
			deps: ['jquery-nos']
		};
</script>
<script src="<?= $require ?>" type="text/javascript"></script>
<?= $js ?>
</head>


<body>
	<?= !empty($body) ? $body : '' ?>
</body>
</html>