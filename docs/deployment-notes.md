# Deployment Notes

## Deploying project on more than one server ?

1. if the project is deployed on multiple servers (ex: DB server, Storage server, Apache server, Queue processor server
...) and there are different document roots on servers (apache: `/var/www/kitline` | queue processor: 
`/home/developer/kitline` ) so tasks which are related to files of the project will face error, because the path saved 
for files uses in task is not the same in Queue processor server. so you need to create a symbolic link to the document 
root of the project in the path which document root is placed in Apache Server.

```bash
ln -s /home/developer/kitline /var/www/kitline #in the queue processor server.
```