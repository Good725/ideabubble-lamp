<h1>Kohana Environment</h1>

<table class="table">
	<tr>
		<th>Kohana Version</th>
		<td><?php echo Kohana::VERSION . ' - <em>' . Kohana::CODENAME . '</em>' ?></td>
	</tr>
	<tr>
		<th>EXT</th>
		<td><?php echo EXT ?></td>
	</tr>
	<tr>
		<th>DOCROOT</th>
		<td><?php echo DOCROOT ?></td>
	</tr>
	<tr>
		<th>APPPATH</th>
		<td><?php echo APPPATH ?></td>
	</tr>
	<tr>
		<th>SYSPATH</th>
		<td><?php echo SYSPATH ?></td>
	</tr>
	<tr>
		<th>MODPATH</th>
		<td><?php echo MODPATH ?></td>
	</tr>
	<tr>
		<th>Kohana::$environment</th>
		<td>
			<?php

			$environment = array(
				10 => 'Production',
				20 => 'Staging',
				30 => 'Testing',
				40 => 'Development',
			);

			echo $environment[Kohana::$environment];

			?>
		</td>
	</tr>
	<tr>
		<th>Kohana::init() settings</th>
		<td>
			<code>
				"base_url" = <?php echo Debug::dump(Kohana::$base_url) ?><br/>
				"index_file" = <?php echo Debug::dump(Kohana::$index_file) ?><br/>
				"charset" = <?php echo Debug::dump(Kohana::$charset) ?><br/>
				"cache_dir" = <?php echo Debug::dump(Kohana::$cache_dir) ?><br/>
				"errors" = <?php echo Debug::dump(Kohana::$errors) ?><br/>
				"profile" = <?php echo Debug::dump(Kohana::$profiling) ?><br/>
				"caching" = <?php echo Debug::dump(Kohana::$caching) ?>
			</code>
		</td>
	</tr>
</table>

<h2>Loaded Modules</h2>

<?php if (count(Kohana::modules()) > 0): ?>
<table class="table">
	<?php foreach (Kohana::modules() as $module => $path): ?>
	<tr>
		<th><?php echo $module ?></th>
		<td><?php echo $path ?>
			<?php if (is_file($path . 'init.php'))
				echo ' <small><em>(has init.php file)<em></small>'; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<p>No modules loaded</p>
<?php endif; ?>

<h2>install.php tests</h2>

<table class="table">
	<tr>
		<th>PHP Version</th>
		<?php if (version_compare(PHP_VERSION, '5.2.3', '>=')): ?>
		<td class="pass"><?php echo PHP_VERSION ?></td>
		<?php else: $failed = TRUE ?>
		<td class="fail">Kohana requires PHP 5.2.3 or newer, this version is <?php echo PHP_VERSION ?>.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>System Directory</th>
		<?php if (is_dir(SYSPATH) AND is_file(SYSPATH . 'classes/kohana' . EXT)): ?>
		<td class="pass"><?php echo SYSPATH ?></td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The configured system directory does not exist or does not contain required files.
		</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Application Directory</th>
		<?php if (is_dir(APPPATH) AND is_file(APPPATH . 'bootstrap' . EXT)): ?>
		<td class="pass"><?php echo APPPATH ?></td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The configured application directory does not exist or does not contain required files.
		</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Cache Directory</th>
		<?php if (is_dir(Kohana::$cache_dir) AND is_writable(Kohana::$cache_dir)): ?>
		<td class="pass"><?php echo Kohana::$cache_dir ?></td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The <?php echo Kohana::$cache_dir ?> directory is not writable.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Logs Directory</th>
		<?php if (is_dir(APPPATH . 'logs') AND is_writable(APPPATH . 'logs')): ?>
		<td class="pass"><?php echo APPPATH . 'logs/' ?></td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The <?php echo APPPATH . 'logs/' ?> directory is not writable.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>PCRE UTF-8</th>
		<?php if (!@preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
		<td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support.</td>
		<?php elseif (!@preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
		<td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with Unicode property support.
		</td>
		<?php else: ?>
		<td class="pass">Pass</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>SPL Enabled</th>
		<?php if (function_exists('spl_autoload_register')): ?>
		<td class="pass">Pass</td>
		<?php else: $failed = TRUE ?>
		<td class="fail">PHP <a href="http://www.php.net/spl">SPL</a> is either not loaded or not compiled in.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Reflection Enabled</th>
		<?php if (class_exists('ReflectionClass')): ?>
		<td class="pass">Pass</td>
		<?php else: $failed = TRUE ?>
		<td class="fail">PHP
			<a href="http://www.php.net/reflection">reflection</a> is either not loaded or not compiled in.
		</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Filters Enabled</th>
		<?php if (function_exists('filter_list')): ?>
		<td class="pass">Pass</td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The
			<a href="http://www.php.net/filter">filter</a> extension is either not loaded or not compiled in.
		</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Iconv Extension Loaded</th>
		<?php if (extension_loaded('iconv')): ?>
		<td class="pass">Pass</td>
		<?php else: $failed = TRUE ?>
		<td class="fail">The <a href="http://php.net/iconv">iconv</a> extension is not loaded.</td>
		<?php endif ?>
	</tr>
	<?php if (extension_loaded('mbstring')): ?>
	<tr>
		<th>Mbstring Not Overloaded</th>
		<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE ?>
		<td class="fail">The
			<a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP's native string functions.
		</td>
		<?php else: ?>
		<td class="pass">Pass</td>
		<?php endif ?>
	</tr>
	<?php endif ?>
	<tr>
		<th>Character Type (CTYPE) Extension</th>
		<?php if (!function_exists('ctype_digit')): $failed = TRUE ?>
		<td class="fail">The <a href="http://php.net/ctype">ctype</a> extension is not enabled.</td>
		<?php else: ?>
		<td class="pass">Pass</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>URI Determination</th>
		<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
		<td class="pass">Pass</td>
		<?php else: $failed = TRUE ?>
		<td class="fail">Neither $_SERVER['REQUEST_URI'], $_SERVER['PHP_SELF'], or $_SERVER['PATH_INFO'] is available.
		</td>
		<?php endif ?>
	</tr>
</table>

<h3>Optional Tests</h3>

<p>
	The following extensions are not required to run the Kohana core, but if enabled can provide access to additional classes.
</p>

<table class="table">
	<tr>
		<th>cURL Enabled</th>
		<?php if (extension_loaded('curl')): ?>
		<td class="pass">Pass</td>
		<?php else: ?>
		<td class="fail">Kohana requires <a href="http://php.net/curl">cURL</a> for the Remote class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>mcrypt Enabled</th>
		<?php if (extension_loaded('mcrypt')): ?>
		<td class="pass">Pass</td>
		<?php else: ?>
		<td class="fail">Kohana requires <a href="http://php.net/mcrypt">mcrypt</a> for the Encrypt class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>GD Enabled</th>
		<?php if (function_exists('gd_info')): ?>
		<td class="pass">Pass</td>
		<?php else: ?>
		<td class="fail">Kohana requires <a href="http://php.net/gd">GD</a> v2 for the Image class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>PDO Enabled</th>
		<?php if (class_exists('PDO')): ?>
		<td class="pass">Pass</td>
		<?php else: ?>
		<td class="fail">Kohana can use <a href="http://php.net/pdo">PDO</a> to support additional databases.</td>
		<?php endif ?>
	</tr>
</table>

<style type="text/css">
	code {
		border: none;
	}
	table td.pass {
		color: #191;
	}
	table td.fail {
		color: #911;
	}

</style>