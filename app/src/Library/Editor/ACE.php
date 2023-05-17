<?php
/**
 * @package   panopticon
 * @copyright Copyright (c)2023-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Panopticon\Library\Editor;

defined('AKEEBA') || die;

use Akeeba\Panopticon\Factory;
use Awf\Utils\Template;
use Delight\Random\Random;

/**
 * Integration of the TinyMCE editor with Panopticon
 */
abstract class ACE
{
	private static bool $hasInitialised = false;

	/**
	 * Displays an ACE source code editor.
	 *
	 * The available syntax highlighting modes are:
	 * - `plain_text` No syntax highlighting
	 * - `css` CSS
	 * - `html` HTML5
	 * - `php` PHP
	 * - `php_laravel_blade` Blade view templates
	 *
	 * @param   string       $name     The name of the form input control
	 * @param   string|null  $content  Initial content
	 * @param   string       $mode     Syntax highlighting mode
	 *
	 * @return string
	 */
	public static function editor(string $name, ?string $content, string $mode = 'css'): string
	{
		self::initialise();

		$document = Factory::getContainer()->application->getDocument();
		$content  = $content ?: '';
		$id       = 'c9aceEditor_' . Random::alphaLowercaseString(32);

		if (!in_array($mode, ['plain_text', 'css', 'html', 'php', 'php_laravel_blade']))
		{
			$mode = 'plain_text';
		}

		// See https://github.com/ajaxorg/ace/wiki/Configuring-Ace
		$options = [
			'highlightActiveLine'       => true,
			'behavioursEnabled'         => true,
			'copyWithEmptySelection'    => true,
			'highlightGutterLine'       => true,
			'showPrintMargin'           => false,
			'theme'                     => 'ace/theme/dracula',
			'newLineMode'               => 'unix',
			'mode'                      => 'ace/mode/' . $mode,
			'enableBasicAutocompletion' => true,
			'enableLiveAutocompletion'  => true,
		];

		$document->addScriptOptions('panopticon.aceEditor', [
			$id => $options,
		]);

		$js = <<< JS
window.addEventListener('DOMContentLoaded', () => {
    const panopticonAceInit = () => {
        if (!ace) return;
        window.clearInterval(waitHandlerTimeout);

        const editor = ace.edit('$id');
        const options = akeeba.System.getOptions('panopticon.aceEditor')['$id'];
        editor.setOptions(options);
        editor.session.on('change', (delta) => {
            document.getElementById('{$id}_textarea').value = editor.getValue();
        })
    };
    
    const waitHandlerTimeout = window.setInterval(panopticonAceInit, 100);
});

JS;
		$document->addScriptDeclaration($js);

		$contentAlt = htmlentities($content);

		return <<<HTML
<textarea id="{$id}_textarea" name="$name" class="d-none">$content</textarea>
<div id="$id">$contentAlt</div>

HTML;
	}

	/**
	 * Initialises the TinyMCE editor JavaScript.
	 *
	 * This method is safe to call multiple times. It will only execute once.
	 *
	 * You can modify the editor configuration by handling the onTinyMCEConfig event. The callback signature is:
	 * ```
	 * function onTinyMCEConfig(array &$config): void
	 * ```
	 *
	 * @return void
	 */
	protected static function initialise()
	{
		if (self::$hasInitialised)
		{
			return;
		}

		self::$hasInitialised = true;

		// Include the JavaScript
		Template::addJs('media://ace/ace.js');
		Template::addJs('media://ace/ext-language_tools.js');
		Template::addCss('media://ace/css/ace.css');
	}
}