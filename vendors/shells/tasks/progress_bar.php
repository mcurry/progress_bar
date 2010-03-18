<?php
/*
 * CakePHP shell task for doing a simple progress bar
 * Copyright (c) 2010 Matt Curry
 * www.PseudoCoder.com
 * http://github.com/mcurry/progress_bar
 *
 * @author      Matt Curry <matt@pseudocoder.com>
 * @license     MIT
 *
 */

/**
 * Progress Bar Task
 *
 * @package progress_bar
 * @subpackage progrss_bar.vendors.shells.tasks
 */
class ProgressBarTask extends Shell {

/**
 * Maximum value on the bar
 *
 * @var int
 * @access public
 */
	var $total = 100;

/**
 * Size
 *
 * @var int
 * @access public
 */
	var $size = 25;

/**
 * Amount Completed
 *
 * @var int
 * @access public
 */
	var $done = 0;

/**
 * Start Time
 *
 * @var mixed
 * @access public
 */
	var $startTime = null;

/**
 * Execute the task
 *
 * @return void
 * @access public
 */
	function execute() {	}

/**
 * Start
 *
 * @param string $total Total value of the progress bar
 * @return void
 * @access public
 */
	function start($total) {
		$this->total = $total;
		$this->done = 0;
		$this->startTime = time();
	}

/**
 * Increment the progress
 *
 * @return void
 * @access public
 */
	function next() {
		$this->done++;
		$this->set();
	}

/**
 * Set the values and output
 *
 * @param string $done Amount completed
 * @return void
 * @access public
 */
	function set($done = null) {
		if ($done) {
			$this->done = min($done, $this->total);
		}

		$perc = round($this->done / $this->total, 3);
		$doneSize = floor($perc * $this->size);

		echo sprintf(
			"\r[%s>%s] %.01f%% %d/%d %s %s%s",
			str_repeat("-", $doneSize),
			str_repeat(" ", $this->size - $doneSize),
			$perc * 100,
			$this->done, $this->total,
			$this->niceRemaining(),
			__('remaining', true),
			str_repeat(' ', 10));
		flush();
	}

/**
 * Calculate remaining time in a nice format
 *
 * @return void
 * @access public
 */
	function niceRemaining() {
		$now = time();
		if($now == $this->startTime || $this->done == 0) {
			return '?';
		}
		
		$rate = ($this->startTime - $now) / $this->done;
		$remaining = -1 * round($rate * ($this->total - $this->done));
		
		if ($remaining < 60) {
			return sprintf('%d secs', $remaining);
		} else {
			return sprintf('%d mins, %d secs', floor($remaining / 60), $remaining % 60);
		}
	}
}
?>