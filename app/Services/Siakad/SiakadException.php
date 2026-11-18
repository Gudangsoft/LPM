<?php

namespace App\Services\Siakad;

use RuntimeException;

/** Kegagalan komunikasi dengan API SIAKAD; pesannya aman ditampilkan ke admin. */
class SiakadException extends RuntimeException
{
    /** error_code dari SIAKAD (mis. "02" login gagal, "05" format parameter/filter salah). */
    public ?string $siakadCode = null;
}
