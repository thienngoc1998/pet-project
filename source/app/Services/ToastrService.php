<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Session;

class ToastrService
{
    private const TOASTR = 'toastr';

    public function show(array $params): void
    {
        Session::flash(self::TOASTR, $params);
    }
}
