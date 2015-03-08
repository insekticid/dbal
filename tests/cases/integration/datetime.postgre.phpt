<?php

/**
 * @testCase
 * @dataProvider? ../../databases.ini postgre
 */

namespace NextrasTests\Dbal;

use DateTime;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


class DateTimePostgreTest extends IntegrationTestCase
{

	public function testWriteStorageTZUTC()
	{
		$connection = $this->createConnection([
			'simpleStorageTz' => 'UTC',
			'connectionTz' => 'Europe/Prague',
		]);

		$connection->query('DROP TABLE IF EXISTS dates_write');
		$connection->query('
			CREATE TABLE dates_write (
				a timestamp,
				b timestamptz
			);
		');

		$connection->query(
			'INSERT INTO dates_write VALUES (%dts, %dt)',
			new DateTime('2015-01-01 12:00:00'), // 11:00 UTC
			new DateTime('2015-01-01 12:00:00')  // 11:00 UTC
		);

		$result = $connection->query('SELECT * FROM dates_write');
		$result->setValueNormalization(FALSE);

		$row = $result->fetch();
		Assert::same('2015-01-01 11:00:00', $row->a);
		Assert::same('2015-01-01 12:00:00+01', $row->b);
	}


	public function testReadStorageTZUTC()
	{
		$connection = $this->createConnection([
			'simpleStorageTz' => 'UTC',
			'connectionTz' => 'Europe/Prague',
		]);

		$connection->query('DROP TABLE IF EXISTS dates_read');
		$connection->query('
			CREATE TABLE dates_read (
				a timestamp,
				b timestamptz
			);
		');

		date_default_timezone_set('Europe/Kiev');
		$connection->query(
			'INSERT INTO dates_read VALUES (%s, %s)',
			'2015-01-01 12:00:00', // 12:00 UTC
			'2015-01-01 12:00:00'  // 11:00 UTC
		);

		$result = $connection->query('SELECT * FROM dates_read');

		$row = $result->fetch();
		Assert::same('2015-01-01T14:00:00+02:00', $row->a->format('c'));
		Assert::same('2015-01-01T13:00:00+02:00', $row->b->format('c'));
	}


	public function testReadStorageTZSame()
	{
		$connection = $this->createConnection([
			'simpleStorageTz' => 'Europe/Prague',
			'connectionTz' => 'Europe/Prague',
		]);

		$connection->query('DROP TABLE IF EXISTS dates_read2');
		$connection->query('
			CREATE TABLE dates_read2 (
				a timestamp,
				b timestamptz
			);
		');

		date_default_timezone_set('Europe/Kiev');
		$connection->query(
			'INSERT INTO dates_read2 VALUES (%s, %s)',
			'2015-01-01 12:00:00', // 11:00 UTC
			'2015-01-01 12:00:00'  // 11:00 UTC
		);

		$result = $connection->query('SELECT * FROM dates_read2');

		$row = $result->fetch();
		Assert::same('2015-01-01T13:00:00+02:00', $row->a->format('c'));
		Assert::same('2015-01-01T13:00:00+02:00', $row->b->format('c'));
	}

}


$test = new DateTimePostgreTest();
$test->run();
