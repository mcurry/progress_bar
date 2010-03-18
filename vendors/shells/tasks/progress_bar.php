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

class ProgressBarTask extends Shell {
	var $total = 100;
	var $size = 25;
	var $done = 0;
	var $startTime = null;

	function execute() {	}
	
	function start($total) {
		$this->total = $total;
		$this->done = 0;
		$this->startTime = time();
	}

	function next() {
		$this->done ++;
		$this->set();
	}

	function set($done=null) {
		if ($done) {
			$this->done = min($done, $this->total);
		}

		$perc = round($this->done / $this->total, 3);
		$doneSize = floor($perc * $this->size);

		$output = sprintf("\r[%s>%s] %.01f%% %d/%d %s remaining%s",
											str_repeat("-", $doneSize),
											str_repeat(" ", $this->size - $doneSize),
											$perc * 100,
											$this->done, $this->total,
											$this->niceRemaining(),
											str_repeat(' ', 10));
		echo $output;
		flush();
	}
	
	function niceRemaining() {
		$now = time();
		if($now == $this->startTime) {
			return '?';
		}
		
		$rate = ($this->startTime - $now) / $this->done;
		$remaining = -1 * round($rate * ($this->total - $this->done));
		
		if ($remaining < 60) {
			return sprintf('%d secs', $remaining);
		} else {
			return sprintf('%d mins, %02d secs', floor($remaining / 60), $remaining % 60);
		}
	}
}
?>