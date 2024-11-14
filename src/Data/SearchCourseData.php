<?php

namespace App\Data;

use App\Entity\Sections;

class SearchCourseData
{
    /**
     * @var integer
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Sections[]
     */
    public $sections = [];
}