<?php

declare(strict_types=1);

namespace Edf\Core;

/**
 * Marker interface yang menyatakan sebuah Backed Enum adalah error code.
 *
 * Error code diambil dari value Backed Enum, sehingga interface ini sengaja
 * tidak mendefinisikan method apa pun.
 */
interface ErrorCode
{
}
