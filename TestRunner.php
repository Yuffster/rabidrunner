<?

define('TEST_DIR', 'tests'); //Amazing configuration.

/**
 * A simple TestRunner class for running unit tests.
 * @author Michelle Steigerwalt <http://msteigerwalt.com>
 * @license MIT
 */
class TestRunner {

	protected $testObj;               //The instantiation of the Test class.
	private   $runMultiple = false;   //Is this object for multiple tests?
	private   $testsToRun  = Array(); //Tests to run.

	//We'll use these properties when outputting the results.
	public $name;              //The name of the test class minus /Test$/.
	public $results = Array(); //An array of each test and its result. 
	public $passing = 0;       //Total number of passed tests.
	public $failing = 0;       //Total number of failed tests.
	public $expectedFailures;  //Number of expected failures to forgive.


	/**
	 * The TestRunner can be constructed either by passing the name of 
	 * one test as a string, an array of test names, or nothing.
	 * If nothing is passed, all tests in the test directory will be loaded
	 * and run.
	 */
	public function __construct($test = null) {
		//If no test is passed, find an run all tests in the test directory.
		if (is_array($test)) $this->testsToRun = $test;
		if ($test == null || is_array($test)) {
			$this->runMultiple = true;
		//Otherwise, run this one specific test.
		} else {
			$this->loadTestClass($test);
			$this->name  = $test;
			$testClass = $test."Test";
			$this->testObj   = new $testClass();
			$this->expectedFailures = $this->testObj->expectedFailures;
		}
	}

	/**
	 * Runs all the tests in the test class and returns the result array.
	 */
	public function runTests() {
		//Handle a bunch of test classes.
		if ($this->runMultiple == true) { return $this->runMultipleTests();  }
		//Handle a single test class.
		$this->testObj->before();
		$ref = new ReflectionClass($this->testObj);
		$methods = $ref->getMethods();
		foreach ($methods as $test) {
			if (preg_match('/^should_/', $test->name)) {
				$this->doTest($test->name);
			}
		}
		$this->testObj->after();
		return $this->results;
	}

	/**
	 * Runs a specific method in a test class and records the result.
	 */
	private function doTest($meth) {
		$this->testObj->beforeEach();
		$error = null;

		//Saves error message of exceptions to record as failures.
		try                  { $this->testObj->$meth();   }
		catch (Exception $e) { $error = $e; $message = $e->getMessage(); }

		//Handler methods allow for expanding base test functionality with
		//application- and platform-specific needs.
		preg_match("/^should_(.*?)_/", $meth, $match);
		$handlerMeth = "handle_should_$match[1]";
		if (method_exists($this, $handlerMeth)) { 
			$message = $this->$handlerMeth($error);
			if ($message) $error = true;
			else $error = null; //Reset the error to false.
		}

		//Determines whether or not the test has succeeded.
		$status = ($error) ? 0 : 1;
		if ($status === 1) $this->passing++;
		else $this->failing++;
		$this->results[$meth] = Array('result'=>$status, 'message'=>$message);
		$this->testObj->afterEach();
	}

	/** 
	 * Loads the file containing the test class data for use elsewhere.  Really
	 * meat and potatoes, this method, since I already have a hierarchical class
	 * loading feature packaged with RabidCore.
	 *
	 * If someone wants to scratch this itch and improve the method, feel free.
	 * I just don't like copying and pasting my own code around.
	 */
	protected function loadTestClass($testName) {
		$f = TEST_DIR."/$testName"."Test.php";
		if (file_exists($f) || !class_exists($testName.'Test')) require_once($f);
	}

	/**
	 * Runs all the tests in the $testToRun property.
	 */
	protected function runMultipleTests() {
		$return = Array();
		//If we don't have a list of tests to run, search the test dir.
		if (!count($this->testsToRun)) {
			$dir = opendir(TEST_DIR);
			while (false !== ($f = readdir($dir))) {
				if (!preg_match('/Test.php$/', $f)) continue;
				$name = preg_replace('/Test.php/', '', $f);
				$this->testsToRun[] = $name;
			}
		} 
		//Run the tests and return the result.
		foreach ($this->testsToRun as $test) {
			$runner = new TestRunner($test);
			$runner->runTests();
			$return[] = $runner;
		} return $return;
	}

	/**
	 * For testing methods which should throw exceptions.
	 * Also serves as an example of a custom test handler.
	 */
	protected function handle_should_fail($error) {
		if (!$error) return "Failed to throw an exception.";
	}

}

?>
