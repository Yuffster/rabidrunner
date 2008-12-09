<?
	include('TestRunner.php');
	include('Test.php');
	$runner = new TestRunner();
	$results = $runner->runTests();
?>

<? foreach ($results as $test): ?>

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
