<?php
/**
 * @package   panopticon
 * @copyright Copyright (c)2023-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Panopticon\Library\Task;

defined('AKEEBA') || die;

use Awf\Registry\Registry;

interface CallbackInterface
{
	public function __invoke(object $task, Registry $storage): int;

	public function getTaskType(): string;

	public function getDescription(): string;
}