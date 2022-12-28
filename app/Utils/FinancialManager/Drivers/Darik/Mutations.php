<?php

namespace App\Utils\FinancialManager\Drivers\Darik;

class Mutations {

    const CREATE_CUSTOMER =
<<<MUTATION
mutation {
    customer_createCustomer(input: {
        companyName: ":companyName"
        ecoCode: ":ecoCode"
        email: ":email"
        gender: :gender
        mobile: ":mobile"
        name: ":name"
        nationalCode: ":nationalCode"
        nationalId: ":nationalId"
        personType: :personType
        phone: ":phone"
        registrationCode: ":registrationCode"
    }) {
        result {
            relationId
        }
        status
    }
}
MUTATION;

    const EDIT_CUSTOMER =
        <<<MUTATION
mutation {
    customer_editCustomer(input: {
        companyName: ":companyName"
        ecoCode: ":ecoCode"
        email: ":email"
        gender: :gender
        mobile: ":mobile"
        name: ":name"
        nationalCode: ":nationalCode"
        nationalId: ":nationalId"
        personType: :personType
        phone: ":phone"
        registrationCode: ":registrationCode"
        relationId: :relationId
    }) {
        result {
            relationId
        }
        status
    }
}
MUTATION;

}