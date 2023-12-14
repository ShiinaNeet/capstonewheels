<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnrolledStudents implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function view(): View
    {
        $data = $this->data;
        return view('exports.enrolled_students', $data);
    }
}
