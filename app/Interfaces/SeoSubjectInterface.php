<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/2/17
 * Time: 4:27 PM
 */

namespace App\Interfaces;


interface SeoSubjectInterface
{
    /*
     *  Relation Methods
     */
    public function review();
    public function createReview();
    public function updateReview();

    /*
     *  Helper Methods
     */
    public function getAdminEditUrl();
    public function getType();
    public function getTitle();
    public function getFrontUrl();

    /*
     *  Getter Methods
     */
    public function getSeoDescription();
    public function getSeoKeywords();
    public function getSeoTitle();
}
