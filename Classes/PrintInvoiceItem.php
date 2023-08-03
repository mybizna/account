<?php

namespace Modules\Account\Classes;

/* A wrapper to do organise item names & prices into columns */

class PrintInvoiceItem
{
    /**
     * Name of the Invoice Item
     *
     * @var string $name
     */
    private $name;

    /**
     * Price of the Invoice Item
     *
     * @var float $price
     */
    private $price;

    /**
     * Whether to show the dollar sign or not
     *
     * @var string $dollarSign
     */
    private $dollarSign;

    /**
     * Constructor for the class PrintInvoiceItem
     *
     * @param string $name
     * @param float $price
     * @param string $dollarSign
     */
    public function __construct($name = null, $price = null, $dollarSign = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    /**
     * Get as string
     *
     * @param int $width
     *
     * @return string
     */
    public function getAsString($width = 48)
    {
        $rightCols = 10;
        $leftCols = $width - $rightCols;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }

    /**
     * Get as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getAsString();
    }

}
