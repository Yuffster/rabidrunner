Introduction
==============

RabidRunner is a  super-lightweight PHP5 test suite by Michelle Steigerwalt.
Meant to be a companion project to RabidCore <http://rabidcore.com>,
RabidRunner follows the same principles, namely accomplishing the task at hand
in the most code-efficient manner possible.

The entire package consists of two classes, TestRunner and Test, along with an
HTML frontend for reviewing the results and an example Test class to test the
runner itself.

This allows the end user to get up and running as quickly as possible without
having to dedicate an inordinate amount of time to understanding the codebase.
The end result is a lightweight but powerful foundation which can be easily
extened to fit specific developer needs.

Basic Usage
==============

To use the TestRunner, create a new class which ends in 'Test' and save it in
the TEST_DIR defined at the top of TestRunner.php (the default is tests/). The
file name should be the same as the class name, and the class should extend the
base Test class.

Test methods begin with 'should_'.

Running Tests
==============

To run tests, create a new TestRunner (or child) object.   The constructor
method takes three distinct parameters:

	NULL   : The TestRunner will search through the test directory for every
		     file ending in Test.php, and add each test to the test queue.

	String : The name of the test to run.  Ie, TestRunner, DataModel, Alarm.
		     The name will prepended with 'Test' and the appropriate file will
		     be loaded from the tests directory.  Ie, TestRunner will invoke
		     TestRunnerTest.php, or the TestRunnerTest class.

	Array  : An array of strings is the equivalent of creating multiple
	         TestRunner objects.

Calling the runTests() metho of the TestRunner object will return the result of
the operation.  Use a print_r on the resulting data to explore its contents.

Failures and Success
==============

By default, tests are considered failures when they raise an exception by using
either a throw statement or the provided Test::assert method.

See the section on advanced handler methods for the exception to this rule. 

Before and After Methods
==============

Each Test class supports four methods: before, after, beforeEach and afterEach.

Overriding these methods will result the method being run before the entire 
series of tests, after the entire series of test, before each test or after
each test.

Advanced Handler Methods
==============

Special handlers can be defined by creating a method on the TestRunner class
beginning with 'handle_' and followed by a set of alphanumeric characters
describing the condition.  Any test methods beginning with 
handle_should_{condition} will be delegated to the specified handler method.

The handler method will be passed the Exception object from the run of the test
method invoking it, or null if no Exception was caught.

If this handler method returns a string or true value, the result it is testing
will be considered a failure, and the string will be recorded as the error 
message.  If the handler method returns a null or false value, the result will
be considered a success.

For example, included with the TestRunner class is the method named
'handle_should_fail':

	protected function handle_should_fail($error) {
		if (!$error) return "Failed to throw an exception.";
	}

This method would result in the following test PASSING:

	public function should_fail_on_bad_math() {
		$this->assert(1==2, "One is equal to two.");
	}

The success or failure of any  methods in a Test class beginning with 
'should_fail' will be determined by the result of this method.

As another example, let's say that we were testing an alarm system.  For
several of our tests, we need to know whether the system is currently buzzing
to alert people nearby.

We could write all our tests with an assertion at the end to ensure that the 
alarm is buzzing, like so:

	class AlarmTest extends Test {

		//[...]

		public function should_buzz_when_wire_is_tripped() {
			$this->alarm->tripWire();
			$this->assert($this->alarm->buzzing == true);
		}

		public function should_buzz_when_noise_exceeds_100db() {
			$this->alarm->registerNoise(101);
			$this->assert($this->alarm->buzzing == true);
		}

		public function should_buzz_if_cover_opened() {
			$this->alarm->openCover();
			$this->assert($this->alarm->buzzing == true);
		}

	}

Or we could create a custom handler method and place it within the TestRunner
class (or a child class).  It might look something like the following:

	protected function handle_should_buzz($error) {
		if (!$this->testObj->alarm->buzzing) return "Alarm should buzz.";
	}

Now, each method which begins with should_buzz will automatically check to see
if that the alarm is buzzing.  Please note that it would be a very good idea to
include a call to the buzzer's reset method in the AlarmTest::afterEach method.

License
==============

Copyright (c) 2008 Michelle Steigerwalt  <http://msteigerwalt.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Enjoy!
==============

Have fun with the code.  If you use it, feel free to drop me a line with your
experience.  I rely on feedback from users like you to improve this product and
future products.
