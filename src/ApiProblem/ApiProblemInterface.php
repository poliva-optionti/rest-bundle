<?php

namespace MNC\RestBundle\ApiProblem;

/**
 * Interface ApiProblemInterface
 * @package MNC\RestBundle\ApiProblem
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ApiProblemInterface
{
    /**
     * @return integer
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDetail();

    /**
     * @return
     */
    public function getInstance();
}