<?php

namespace App\Enums\Product;

use App\Common\BaseEnum;

class ProductWatermarkPosition extends BaseEnum {
    const TOP_LEFT = "top_left";
    const TOP_RIGHT = "top_right";
    const BOTTOM_LEFT = "bottom_left";
    const BOTTOM_RIGHT = "bottom_right";
    const CENTER = "center";
    const TOP_CENTER = "top_center";
    const BOTTOM_CENTER = "bottom_center";
    const LEFT_CENTER = "left_center";
    const RIGHT_CENTER = "right_center";
}
