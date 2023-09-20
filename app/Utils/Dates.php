<?php
namespace App\Utils;

use DateTimeImmutable;

class Dates
{
	public static function mesesArr(): array
	{
		return [
			1 => 'Janeiro',
			2 => 'Fevereiro',
			3 => 'Março',
			4 => 'Abril',
			5 => 'Maio',
			6 => 'Junho',
			7 => 'Julho',
			8 => 'Agosto',
			9 => 'Setembro',
			10 => 'Outubro',
			11 => 'Novembro',
			12 => 'Dezembro'
		];
	}

	public static function unixByDate(?string $dataUtc): int
	{
		$data = new DateTimeImmutable($dataUtc ?? 'now');
		return $data->getTimestamp();
	}

	public static function dateNow(): string
	{
		return date('d/m/Y');
	}

	public static function dateTimeNow(): string
	{
		return date('Y-m-d H:i:s');
	}

	public static function ExtDateNow(): string
	{
		return date('d') . ' de ' . self::mesesArr()[date('n')] . ' de ' . date('Y');
	}

	public static function ExtDate(string $dataUtc): string
	{
		$data = new DateTimeImmutable($dataUtc);
		$day = $data->format('d');
		$mount = $data->format('n');
		$year = $data->format('Y');

		return $day . ' de ' . self::mesesArr()[$mount] . ' de ' . $year;
	}

	public static function monthYearByDate(?string $dataUtc): ?string
	{
		$data = new DateTimeImmutable($dataUtc ?? 'now');
		$mount = $data->format('m');
		$year = $data->format('Y');

		return $mount . '/' . $year;
	}

	public static function diffYears(?string $dtnasc): int
	{
		$nasc = new DateTimeImmutable($dtnasc ?? 'now');
		$today = new DateTimeImmutable('now');
		$age = $nasc->diff($today);

		return $age->y;
	}

	public static function diffDays(string $dtorigin, string $dttarget): int
	{
		$origin = new DateTimeImmutable($dtorigin);
		$target = new DateTimeImmutable($dttarget);
		$interval = $origin->diff($target);

		return $interval->invert ? -(int) $interval->days : (int) $interval->days;
	}

	public static function fmtLocal(?string $dataUtc): ?string
	{
		$fmtt = null;
		if ($dataUtc != null) {
			$data = new DateTimeImmutable($dataUtc ?? 'now');
			$fmtt = $data->format('d/m/Y');
		}
		return $fmtt;
	}

	public static function fmtLocalTime(?string $dataUtc): ?string
	{
		$fmtt = null;
		if ($dataUtc != null) {
			$data = new DateTimeImmutable($dataUtc ?? 'now');
			$fmtt = $data->format('d/m/Y H:i:s');
		}
		return $fmtt;
	}

	public static function fmtDB(?string $dataLocal): ?string
	{
		$fmtt = null;
		if ($dataLocal != null) {
			$data = new DateTimeImmutable(self::convetToUTC($dataLocal) ?? 'now');
			$fmtt = $data->format('Y-m-d');
		}
		return $fmtt;
	}

	public static function fmtTimeDB($dataLocal): ?string
	{
		$fmtt = null;
		if ($dataLocal != null) {
			$data = new DateTimeImmutable(self::convertDtTimeToUtc($dataLocal) ?? 'now');
			$fmtt = $data->format('Y-m-d H:i:s');
		}
		return $fmtt;
	}

	public static function validDate(?string $dataUtc): bool
	{

		if ($dataUtc != null && count(explode('-', $dataUtc)) == 3) {
			$mount = date('n', self::unixByDate($dataUtc));
			$day = date('j', self::unixByDate($dataUtc));
			$year = date('Y', self::unixByDate($dataUtc));

			return checkdate($mount, $day, $year);
		}

		return false;
	}

	public static function listYears(int $start = 2010): array
	{
		$years = [];
		while ($start <= date('Y')) {
			$years[$start] = $start;
			$start++;
		}
		arsort($years);
		return $years;
	}

	private static function convetToUTC(?string $localDate): ?string
	{
		$cdate = null;
		$ldate = explode('/', $localDate);

		if ($localDate != null && count($ldate) == 3) {
			$cdate = implode('-', array_reverse($ldate));
		}

		return $cdate;
	}

	private static function convertDtTimeToUtc(?string $locaDateTime): ?string
	{
		$cdate = null;
		$ldate = explode(' ', $locaDateTime);
		if ($locaDateTime != null && count($ldate) == 2) {
			$date = explode('/', $ldate[0]);
			$cdate = implode('-', array_reverse($date)) . ' ' . $ldate[1];
		}

		return $cdate;
	}
}