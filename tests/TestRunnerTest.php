<?

class TestRunnerTest extends Test {

	//This is probably the only series of tests that should need this.
	public $expectedFailures = 2;

	//Should pass.
	public function should_pass_empty_test() { }

	//Should pass.
	public function should_fail_when_starting_with_should_fail() {
		throw new Exception("Failure expected.");
	}

	//Should fail.
	public function should_return_failure() {
		throw new Exception("Failure expected.");
	}

	//Should fail.
	public function should_throw_failure_assertion() {
		$this->assert(1==2, "One is equal to two.");
	}

	//Should pass.
	public function should_pass_true_assertion() {
		$this->assert(1==1, "One is equal to one.");
	}

}

?>
