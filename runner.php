<?
include('TestRunner.php');
include('Test.php');
$runner = new TestRunner();
$results = $runner->runTests();
$f = $_GET['format'];

/******************************************************************************
 /////////////////////////////// OUTPUT XML ///////////////////////////////////
 *****************************************************************************/
if ($f == 'xml'):
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
	<results>
		<?foreach ($results as $test) : ?>
			<set title="<?=$test->name?>">
				<failing expected="<?=$test->expectedFailures?>"><?=$test->failing?></failing>
				<passing><?=$test->passing?></passing>
				<specs>
					<?foreach ($test->results as $name=>$data) : ?>
						<spec>
							<name><?=$name?></name>
							<result><?=$data['result']?></result>
							<message><?=$data['message']?></message>
						</spec>
					<?endforeach;?>
				</specs>
			</set>
		<?endforeach;?>
	</results>
<?

/******************************************************************************
 /////////////////////////////// OUTPUT JSON //////////////////////////////////
 *****************************************************************************/
elseif ($f == 'json' OR 'jsonp'):
	header("Content-Type: text/javascript");
	$return = Array();
	foreach ($results as $test):
		$return[] = Array('title'=>$test->name,
		                  'expectedFailures'=>$test->expectedFailures,
		                  'passing'=>$test->passing,
		                  'failing'=>$test->failing,
		                  'results'=>$test->results);
	endforeach;
	//Optional JSON-P callback wrapper.
	if ($f == 'jsonp') echo "rabidrunner_callback[$_GET[id]](";
	echo json_encode($return);
	if ($f == 'jsonp') echo ");";

/******************************************************************************
 ////////////////////////////// OUTPUT HTML ///////////////////////////////////
 *****************************************************************************/
else: foreach ($results as $test): ?>
		<h2>
			<?= $test->name; ?>:
			<?= $test->passing + $test->expectedFailures ?>/<?=$test->passing+$test->failing?>
			<?if ($test->expectedFailures): ?>
				(<?=$test->expectedFailures?> expected failures)
			<?endif;?>
		</h2>
		<ol>
			<?foreach ($test->results as $name=>$data):?>
				<li class="<?= ($data['result']) ? 'passed' : 'failed';?>">
					<strong><?= ($data['result']) ? 'PASS' : 'FAIL' ?></strong>:
					<?= ucfirst(str_replace('_', ' ', $name)); ?>.
					<? if ($data['message']): ?>
						<span class="message">(<?= $data['message'] ?>)</span>
					<?endif;?>
				</li>
			<?endforeach;?>
		</ol>
	<?endforeach;?>
<?endif;?>
