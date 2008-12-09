<?

/**
 * A very basic Test class.  Meant to be extended.
 * @author Michelle Steigerwalt <http://msteigerwalt.com>
 * @license MIT
 */
class Test {

	public $testObject = null;
	public function before()     { }
	public function after()      { }
	public function beforeEach() { }
	public function afterEach()  { }
	public function assert($bool, $message = null) {
		if ($bool == false) throw new Exception("Assertion failed: $message");
	}

}

?>
