<?php

namespace App;

enum OrderStatus: int
{
    case Open = 0;
    case Filled = 1;
    case Cancelled = 2;
}
