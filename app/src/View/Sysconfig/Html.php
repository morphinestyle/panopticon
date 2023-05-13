<?php
/**
 * @package   panopticon
 * @copyright Copyright (c)2023-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Panopticon\View\Sysconfig;


use Awf\Text\Text;
use Awf\Utils\Template;

defined('AKEEBA') || die;

class Html extends \Awf\Mvc\DataView\Html
{
	protected function onBeforeMain(): bool
	{
		// Load JavaScript
		Template::addJs('media://js/showon.js');

		// Create a save and apply button in the toolbar
		$buttons = [
			[
				'title'   => Text::_('PANOPTICON_BTN_SAVE'),
				'class'   => 'btn btn-primary',
				'onClick' => 'akeeba.System.submitForm(\'save\');',
				'icon'    => 'fa fa-save',
			],
			[
				'title'   => Text::_('PANOPTICON_BTN_APPLY'),
				'class'   => 'btn btn-secondary border-white',
				'onClick' => 'akeeba.System.submitForm(\'apply\');',
				'icon'    => 'fa fa-check',
			],
			[
				'title'   => Text::_('PANOPTICON_BTN_PHPINFO'),
				'class'   => 'btn btn-dark',
				'onClick' => $this->container->router->route('index.php?view=phpinfo'),
				'icon'    => 'fa fa-info-circle',
			],
			[
				'title'   => Text::_('PANOPTICON_BTN_CANCEL'),
				'class'   => 'btn btn-danger',
				'onClick' => $this->container->router->route('index.php'),
				'icon'    => 'fa fa-cancel',
			],
		];

		$toolbar = $this->container->application->getDocument()->getToolbar();

		array_walk($buttons, fn($button) => $toolbar->addButtonFromDefinition($button));

		return true;
	}
}