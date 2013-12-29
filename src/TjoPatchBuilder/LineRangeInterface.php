<?php

namespace TjoPatchBuilder;

interface LineRangeInterface
{
    /**
     * @return int
     */
    public function getStart();

    /**
     * @return int
     */
    public function getEnd();

    /**
     * @return int
     */
    public function getLength();
}
