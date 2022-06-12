# A basic guid for creating finman web services :
**All web services inputs and outputs are json.**

## `getAllCustomers()`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
page | integer | the number of page needed. | No

### Outputs:

type | description | is_important
-----|-------------|-------------
Customer[] | Array of customer objects. | Yes

This method should return the list of all customers existing in db.
Normally there is no input parameter calling this method, but if there is `page` input in parameters the return 
value should be paginated and method returns the content of the page needed.

## `getCustomerByPhone($phoneNumber)`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
phone_number | string | phone number of the customer we are searching for. | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
Customer | customer object matching the phone number. | Yes

Find and return the customer object matching the passed phone number.

## `getCustomerByRelation($relation)`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
relation | string | the property in customer model which links ecommerce customers to accounting system customers. | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
Customer | customer object matching the relation. | Yes

The `relation` property can be customerID or customerCode in accounting system.
actually it doesn't matter that `relation` is which field, what matters is :
1. the uniqueness of the `relation` field.
2. `relation` must be equal to the value which `addCustomer()` method returns.

## addCustomer($customer)

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
Customer | json object | json object containing new customer to create. | Yes
Customer.is_legal_person | bool | is customer legal or not | Yes
Customer.name | string | - (only for normal person) | No
Customer.family | string | - (only for normal person) | No
Customer.main_phone | string | - | No
Customer.national_code | string | - (only for normal person) | No
Customer.email | string | - | No
Customer.gender | bool | True for male/False for female (only for normal person) | No
Customer.economical_code | string | - (only for legal person) | No
Customer.registration_code | string | - (only for legal person) | No
Customer.national_id | string | - (only for legal person) | No
Customer.company_name | string | - (only for legal person) | No

### Outputs:

type | description | is_important
-----|-------------|-------------
string | `relation`  (the `ID` or `Code` of the created customer) | Yes

## `editCustomer($customer)`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
Customer | json object | json object containing new customer to create. | Yes
Customer.relation | string | the `relation` between ecommerce and accounting objects. | Yes
Customer.is_legal_person | bool | is customer legal or not | Yes
Customer.name | string | - (only for normal person) | No
Customer.family | string | - (only for normal person) | No
Customer.main_phone | string | - | No
Customer.national_code | string | - (only for normal person) | No
Customer.email | string | - | No
Customer.gender | bool | True for male/False for female (only for normal person) | No
Customer.economical_code | string | - (only for legal person) | No
Customer.registration_code | string | - (only for legal person) | No
Customer.national_id | string | - (only for legal person) | No
Customer.company_name | string | - (only for legal person) | No

### Outputs:
type | description | is_important
-----|-------------|-------------
string | `relation`  (the `ID` or `Code` of the created customer) | Yes

## `getAllProducts()`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
page | integer | the number of page needed. | No

### Outputs:

type | description | is_important
-----|-------------|-------------
Product[] | Array of customer objects. | Yes

#### Product properties :
 
parameter_name | type 
---------------|------
title | string
price | integer (rls)
quantity | integer/double
relation(product_code) | string

## `getProductByRelation($relation)`

### Inputs:

parameter name | type | description | is_important
----------------|------|-------------|--------------
relation | string | the property in product model which links ecommerce products to accounting system products. | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
Product | product object matching the relation. | Yes

The `relation` property can be `productID` or `productCode` in accounting system.
actually it doesn't matter that `relation` is which field, what matters is :
1. the uniqueness of the `relation` field.
2. `relation` must be equal to the value which `getAllProduct()` method returns as `relation` property.

## `addPreInvoice()`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
PreInvoice | json_object | json object containing new pre_invoice to create. | Yes
PreInvoice.customer_relation | string | - | Yes
PreInvoice.description | string | - | No
PreInvoice.rows[] | json_array | - Yes
PreInvoice.rows[].product_relation | string | - | Yes
PreInvoice.rows[].count | integer | - | Yes  
PreInvoice.rows[].discount_amount | integer(rls) - multiplied to count | - | Yes
PreInvoice.rows[].pure_price | integer(rls) - not multiplied to count | - | Yes
PreInvoice.rows[].tax_amount | integer(rls) - multiplied to count | - | Yes
PreInvoice.rows[].toll_amount | integer(rls) - multiplied to count | - | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
string | `relation`  (the `ID` or `Code` of the created pre invoice) | Yes

## `deletePreInvoice`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
relation | string | the property in pre_invoice model which links ecommerce pre_invoices to accounting system pre_invoices. | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
None | None | None

## `submitWarehousePermission`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
relation | string | the property in pre_invoice model which links ecommerce pre_invoices to accounting system pre_invoices. | Yes

### Outputs:

type | description | is_important
-----|-------------|-------------
string | `relation` of the created warehouse_permission document. | Yes

## `checkExitTab`

### Inputs:

parameter name | type | description | is_important
---------------|------|-------------|--------------
relation | string | the property in pre_invoice/warehouse_permission model which links ecommerce documents to accounting system documents. | Yes

### Outputs:

type | description | is_important
----------------|------|-------------
string | `relation` of exit tab document of exists | Yes
