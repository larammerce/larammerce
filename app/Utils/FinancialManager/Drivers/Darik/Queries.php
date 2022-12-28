<?php

namespace App\Utils\FinancialManager\Drivers\Darik;

class Queries {

    const ALL_CUSTOMERS =
<<<QUERY
query {
    customer_getCustomers {
        result {
            items {
                companyName
                ecoCode
                email
                gender
                mobile
                name
                nationalCode
                nationalId
                personType
                phone
                registrationCode
                relationId
            }
        }
        status
    }
}
QUERY;

    const CUSTOMER_BY_RELATION=
<<<QUERY
query {
    customer_getCustomer(relationId: :relationId) {
        result {
            companyName
            ecoCode
            email
            gender
            mobile
            name
            nationalCode
            nationalId
            personType
            phone
            registrationCode
            relationId
        }
        status
    }
}
QUERY;

    const ALL_PRODUCTS =
        <<<QUERY
query {
    proudct_getProducts {
        result {
            items {
                name
                price
                quantity
                relationId
            }
        }
        status
    }
}
QUERY;

    const PRODUCT_BY_RELATION=
        <<<QUERY
query {
    proudct_getProduct(relationId: :relationId) {
        result {
            name
            price
            quantity
            relationId
        }
        status
    }
}
QUERY;


}