<?php
namespace App\Utils;
use App\Models\Candidato;

class Html
{
	public static function select(array $options = [], ?string $selected = null, bool $showfirst = false): string
	{
		$html = $showfirst ? '' : '<option value=""></option>';
		foreach ($options as $key => $opt) {
			$isselected = ($selected != null && $key == $selected) ? 'selected' : '';
			$html .= '<option value="' . $key . '" ' . $isselected . '>' . $opt . '</option>';
		}
		return $html;
	}

	public static function selectObj(?array $objects, string $index, array $values):string
	{
		$html = '<option value=""></option>';
		if($objects != null){
			foreach ($objects as $object) {
				$opt = [];
				foreach ($values as $value) {
					$opt[] = $object->get($value);
				}
				$html .= '<option value="' . $object->get($index) . '">' .implode(' - ', $opt). '</option>';
			}
		}
		return $html;
	}

	public static function listValues(string $title, int $value):string{
		return '<div class="d-flex content-list-values">
			<div class="title-list-values">'.$title.'</div>
			<div class="value-list-values ms-auto">'.$value.'</div>
		</div>';
	}

	public static function inputvoto(?array $candidatos):string
	{
		$html = '';

		if($candidatos != null){
			foreach ($candidatos as $candidato) {
				$html .= 
				'<div class="col-md-4 mb-3 content-voto">
					<img src="storage/uploads/'.$candidato->get('foto').'" class="foto-table me-3">
					<div class="candidato-view">
						<label for="votos_'.$candidato->get('id').'" class="form-label">'.$candidato->get('nome').' - '.$candidato->get('numero').'</label>
						<input name="votos_'.$candidato->get('id').'" type="text" class="form-control ocp-input masknumb" 
						id="votos_'.$candidato->get('id').'" value="0" data-required="true">
					</div>
				</div>';
			}
		}

		return $html;
	}

	public static function genericTable(?array $header = null, array $body = [], bool $count = true):string
    {
        //build cout total regiters
        $strtotal = count($body) > 1 ? 'Registros Localizados' : 'Registro Localizado';
        $total = $count ? '<div class="table-info">'.str_pad(strval(count($body)), 2, "0", STR_PAD_LEFT).' '.$strtotal.'</div>' : '';
        
        //build header table
        $top  = $header != null ? '<thead><tr>' : '';
        $top .= ($header != null && $count) ? '<th>#</th>' : '';
        if($header != null){foreach ($header as $col) { $top .= '<th>'.$col.'</th>';}}
        $top .= $header != null ? '</tr></thead>' : '';

        //build body table
        $nline   = 1;
        $filling = '';
        foreach($body as $line){
            $filling .= '<tr>';
            $filling .= $count ? '<td>'.$nline++.'</td>' : '';
            foreach($line as $col){ $filling .= '<td>'.$col.'</td>'; }
            $filling .= '</tr>';
        }

        //build table
        $table  = '';
        if($body != null)
        {
            $table  = '<table class="table">';
            $table .= $top.$filling;
            $table .= '</table>';
        }

        return $total.$table;
    }
}