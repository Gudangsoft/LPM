<?php

namespace App\Services\Siakad;

use RuntimeException;

/** Kegagalan komunikasi dengan API SIAKAD; pesannya aman ditampilkan ke admin. */
class SiakadException extends RuntimeException
{
}
