<?php
/**
 * ProgressBarTask Test Cases
 *
 * Test Cases for progress bar shell task
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2010 Matt Curry
 * www.PseudoCoder.com
 * http://github.com/mcurry/progress_bar
 *
 * @author      Matt Curry <matt@pseudocoder.com>
 * @license     MIT
 *
 */
App::import('Shell', 'Shell', false);
App::import('Vendor', 'ProgressBar.ProgressBarTask', array('file' => 'shells' . DS . 'tasks' . DS . 'progress_bar.php'));

if (!defined('DISABLE_AUTO_DISPATCH')) {
	define('DISABLE_AUTO_DISPATCH', true);
}

if (!class_exists('ShellDispatcher')) {
	ob_start();
	$argv = false;
	require CAKE . 'console' .  DS . 'cake.php';
	ob_end_clean();
}

Mock::generatePartial(
	'ShellDispatcher', 'TestProgressBarTaskMockShellDispatcher',
	array('getInput', 'stdout', 'stderr', '_stop', '_initEnvironment')
);

Mock::generatePartial(
	'ProgressBarTask', 'MockProgressBarTask',
	array('in', '_stop', 'err', 'out')
);

/**
 * ProgressBarTask Test class
 *
 * @package       progress_bar
 * @subpackage    progress_bar.tests.cases.vendors.shells.tasks
 */
class ProgressBarTaskTest extends CakeTestCase {

/**
 * startTest method
 *
 * @return void
 * @access public
 */
	function startTest() {
		$this->Dispatcher =& new TestProgressBarTaskMockShellDispatcher();
		$this->Dispatcher->shellPaths = App::path('shells');
		$this->Task =& new MockProgressBarTask($this->Dispatcher);
		$this->Task->Dispatch =& $this->Dispatcher;
		$this->Task->path = TMP . 'tests' . DS;
	}

/**
 * endTest method
 *
 * @return void
 * @access public
 */
	function endTest() {
		ClassRegistry::flush();
	}
	
/**
 * testStartup method
 *
 * @return void
 * @access public
 */
	function testStartup() {
		$total = 100;
		$now = time();
		$this->Task->start($total);
		$this->assertIdentical($this->Task->total, $total);
		$this->assertWithinMargin($this->Task->startTime, time(), 1);
		$this->assertIdentical($this->Task->done, 0);
	}

/**
 * testExecuteNothing method
 *
 * @return void
 * @access public
 */
	function testExecuteNothing() {
		$this->assertNull($this->Task->execute());
	}

/**
 * testNext method
 *
 * @return void
 * @access public
 */
	function testNext() {
		$this->Task->start(100);
		$this->Task->next();
		$this->assertIdentical($this->Task->done, 1);
	}

/**
 * testNiceRemainingUnknown method
 *
 * @return void
 * @access public
 */	
	function testNiceRemainingUnknown() {
		$this->Task->start(100);

		$expected = '?';
		$this->assertEqual($this->Task->niceRemaining(), $expected);

		$this->Task->next();
		$expected = '?';
		$this->assertEqual($this->Task->niceRemaining(), $expected);
	}

/**
 * testNiceRemainingBasic method
 *
 * @return void
 * @access public
 */
	function testNiceRemainingBasic() {
		// 2 seconds per iteration, should take 20 seconds total.
		$total = 10;
		$delay = 2;
		$loops = 3;
		$this->Task->start($total);

		for ($i = 0; $i < $loops; $i++) {
			sleep($delay);
			$this->Task->next();
		}
		$result = $this->Task->niceRemaining();
		$expected = '14 secs';
		$this->assertEqual($result, $expected);

		// Testing numbers not necessarily nice and rounded
		// 2 seconds per iteration, should take 20 seconds total.
		$total = 9;
		$delay = 1;
		$loops = 4;
		$this->Task->start($total);

		for ($i = 0; $i < $loops; $i++) {
			sleep($delay);
			$this->Task->next();
		}
		$result = $this->Task->niceRemaining();
		$expected = '5 secs';
		$this->assertEqual($result, $expected);
	}

/**
 * testNiceRemainingMinutes method
 *
 * @return void
 * @access public
 */
	function testNiceRemainingMinutes() {
		// 2 seconds per iteration, should take 120 seconds total.
		$total = 60;
		$delay = 2;
		$loops = 3;
		$this->Task->start($total);

		for ($i = 0; $i < $loops; $i++) {
			sleep($delay);
			$this->Task->next();
		}
		$result = $this->Task->niceRemaining();
		
		$expected = '1 min, 54 secs';
		$this->assertEqual($result, $expected);

		// 2 seconds per iteration, should take 200 seconds total.
		$total = 120;
		$delay = 2;
		$loops = 3;
		$this->Task->start($total);

		for ($i = 0; $i < $loops; $i++) {
			sleep($delay);
			$this->Task->next();
		}
		$result = $this->Task->niceRemaining();
		
		$expected = '3 mins, 54 secs';
		$this->assertEqual($result, $expected);
	}

/**
 * testSet method
 *
 * @return void
 * @access public
 */
	function testSet() {
		$this->Task->start(100);
		$this->Task->set(50);
		$this->assertEqual($this->Task->done, 50);

		$this->Task->set(200);
		$this->assertEqual($this->Task->done, 100);
	}
}
?>