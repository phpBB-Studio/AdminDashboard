<?php
/**
 *
 * phpBB Studio - Admin Dashboard. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\admindashboard;

/**
 * phpBB Studio - Admin Dashboard: Kasimi
 */
class kasimi
{
	/** @var array Kasimi's brains */
	protected $brain = [];

	/** @var bool Kasimi's not happy */
	protected $happy = false;

	/**
	 * Get knowledge inside Kasimi's brain.
	 *
	 * @return array
	 */
	public function get_brain(): array
	{
		return (array) $this->brain;
	}

	/**
	 * Set knowledge inside Kasimi's brain.
	 *
	 * @param array		$brain		The brain knowledge
	 * @return void
	 */
	public function set_brain(array $brain): void
	{
		$this->brain = $brain;
	}

	/**
	 * Is Kasimi happy?
	 *
	 * @return bool
	 */
	public function is_happy(): bool
	{
		return (bool) $this->happy;
	}

	/**
	 * Make Kasimi happy.
	 *
	 * @param bool		$happy		Kasimi's happy state
	 * @return void
	 */
	public function set_happy(bool $happy): void
	{
		$this->happy = $happy;
	}
}
