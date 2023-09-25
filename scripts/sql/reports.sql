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

-- get list of products and amount of sales on 2 months
select agg2.*, p2.`title`, p2.`code`
from (select product_id, sum(sum_price) as total_sell, sum(buy_count) as total_count
      from (select products.id as product_id, (invoice_rows.product_price * invoice_rows.count) as sum_price, invoice_rows.count as buy_count
            from products,
                 invoice_rows,
                 invoices
            where products.id = invoice_rows.product_id
              and invoice_rows.invoice_id = invoices.id
              and invoices.payment_status in (1, 2)
              and invoices.created_at > '2023-07-22 00:00:00'
              and invoices.created_at < '2023-09-23 00:00:00') as agg1
      group by agg1.product_id) as agg2,
     products as p2
where p2.id = agg2.product_id