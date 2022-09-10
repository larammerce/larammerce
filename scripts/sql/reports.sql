select count(id)
from users
where created_at >= '2022-03-21 00:00:00'
  and created_at <= '2023-03-20 00:00:00';

select count(distinct (users.id))
from users,
     customer_users,
     invoices
where users.id = customer_users.user_id
  and customer_users.id = invoices.customer_user_id
  and invoices.shipment_status >= 1
  and users.created_at >= '2022-03-21 00:00:00'
  and users.created_at <= '2023-03-20 00:00:00';

select users.name, users.family, users.username, sum(invoices.sum), count(invoices.id)
from users,
     customer_users,
     invoices
where users.id = customer_users.user_id
  and customer_users.id = invoices.customer_user_id
  and invoices.shipment_status >= 1
  and invoices.created_at >= '2022-03-21 00:00:00'
  and invoices.created_at <= '2023-03-20 00:00:00'
group by users.id

select count(directories.id), directories.title
from directories,
     directory_product,
     products
where directories.directory_id is null
  and directories.id = directory_product.directory_id
  and products.id = directory_product.product_id
  and products.is_active = 0
group by directories.id;

select count(directories.id), directories.title
from directories,
     directory_product,
     products
where directories.directory_id is null
  and directories.id = directory_product.directory_id
  and products.id = directory_product.product_id
  and products.is_active = 1
group by directories.id;
