<?php
namespace App\Controllers\Reports;
use App\Controllers\Pages\Page;
use App\Utils\Log;
use App\Utils\View;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

abstract class Report
{
    public function getReport(string $title, string $content, array $params = []):string
    {
        $base = [
            'h_sisname' => Page::APPNAME,
            'h_company' => Page::APPDESC,
            'content'   => View::renderView($content, $params),
        ];

        return View::renderView('reports/master', $base);
    }

    public function export(string $title, mixed $html, string $mode = 'open'):void
    {
        match($mode){
            'open'  => $this->exportOpen($title, $html),
            default => null
        };
    }

    private function exportOpen(string $title, string $html):void
    {
        $style  = file_get_contents(__DIR__ . '/../../../resources/css/report.css');
        $params = [
            'mode'              => 'utf-8',
            'format'            => 'A4-P',
            'default_font_size' => 8,
            'default_font'      => 'Arial'
        ];

        try {
            $mpdf =  new Mpdf($params);
            $mpdf -> WriteHTML($style, HTMLParserMode::HEADER_CSS);
            $mpdf -> WriteHTML($html, HTMLParserMode::HTML_BODY);
            $mpdf -> Output($title.'.pdf', "D");

        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
    }
}