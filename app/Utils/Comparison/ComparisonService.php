<?php

namespace App\Utils\Comparison;

use App\Models\Product;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ComparisonService
{
    private static string $INITIAL_PRODUCT = "initial_product";
    private static string $COMPARING_PRODUCTS = "comparative_products";

    public static function initProduct(Product $product)
    {
        $product->load(["pAttributes"]);
        session()->put(self::$INITIAL_PRODUCT, $product);
        self::forgetComparingProducts();
    }

    public static function getInitialProduct(): ?Product
    {
        if (session()->has(self::$INITIAL_PRODUCT))
            try {
                return session()->get(self::$INITIAL_PRODUCT);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                return null;
            }
        return null;
    }

    public static function forgetInitialProduct()
    {
        session()->forget(self::$INITIAL_PRODUCT);
        self::forgetComparingProducts();
    }

    /**
     * @throws ProductStructureMismatchException
     * @throws InitialProductNotFoundException
     */
    public static function addProduct(Product $product)
    {
        $initialized_product = self::getInitialProduct();
        if ($initialized_product !== null) {
            if ($product->p_structure_id == $initialized_product->p_structure_id) {
                $product->load(["pAttributes"]);
                $products = static::getComparingProducts();
                $products[$product->id] = $product;
                session()->put(self::$COMPARING_PRODUCTS, $products);
            } else
                throw new ProductStructureMismatchException();
        } else
            throw new InitialProductNotFoundException();
    }

    /**
     * @throws InitialProductNotFoundException
     */
    public static function removeProduct(Product $product)
    {
        $initialized_product = self::getInitialProduct();
        if ($initialized_product !== null) {
            $products = static::getComparingProducts();
            if (array_key_exists($product->id, $products))
                unset($products[$product->id]);
            if (count($products) > 0) {
                session()->put(self::$COMPARING_PRODUCTS, $products);
            } else {
                static::forgetComparingProducts();
            }
        } else
            throw new InitialProductNotFoundException();
    }

    public static function getComparingProducts()
    {
        try {
            return session()->get(self::$COMPARING_PRODUCTS) ?: [];
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return [];
        }
    }

    public static function forgetComparingProducts()
    {
        session()->forget(self::$COMPARING_PRODUCTS);
    }
}
