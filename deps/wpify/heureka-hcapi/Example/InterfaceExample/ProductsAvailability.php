<?php

namespace HeurekaDeps\Example\InterfaceExample;

use HeurekaDeps\Hcapi\Interfaces\IShopImplementation;
/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class ProductsAvailability implements IShopImplementation
{
    /**
     * @param array $receiveData
     *
     * @return array
     */
    public function getResponse($receiveData)
    {
        //get data from database about products
        return ['products' => [['id' => 'ABC123', 'available' => \true, 'count' => 1, 'delivery' => 0, 'name' => 'Diesel Zero Plus Masculine', 'price' => 100.0, 'related' => [['title' => 'Zdarma dárková taška']], 'params' => [['id' => 2, 'type' => 'input', 'name' => 'Délka', 'unit' => 'm', 'values' => [[]]], ['id' => 2, 'type' => 'selectbox', 'name' => 'barva', 'unit' => '', 'values' => [['id' => 2, 'default' => \false, 'value' => 'cervena', 'price' => 10.6], ['id' => 2, 'default' => \true, 'value' => 'cerna', 'price' => 0.0]]]], 'priceTotal' => 100.0], ['id' => 'ABC124', 'available' => \true, 'count' => 2, 'delivery' => 'na dotaz', 'name' => 'Mikrovlnná trouba Ariete-Scarlett 933 nerez', 'price' => 200.0, 'related' => [['title' => 'Vynáška do 5. patra zdarma'], ['title' => 'Propiska zdarma.']], 'priceTotal' => 400.0]], 'priceSum' => 500.0];
    }
}
