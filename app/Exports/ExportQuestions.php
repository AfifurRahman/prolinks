<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Discussion;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportQuestions implements FromView
{
    protected $data;
	function __construct($report) {
	    $this->data = $report;
	}

    public function view(): View
    {
        return view('adminuser.discussion.export.export_discussion', [
            'report'  => $this->data,
        ]);
    }
}
