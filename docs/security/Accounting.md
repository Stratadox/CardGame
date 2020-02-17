# Accounting
Accounting refers to being able to account for what happened by keeping access- 
and audit logs.

## Resource Access
For most static resources, logging is performed by server software, such as 
[apache](https://httpd.apache.org/docs/2.4/logs.html) or [nginx](https://docs.nginx.com/nginx/admin-guide/monitoring/logging/#access_log).

## Issued commands
Every state-altering action in the system is performed through dispatching - and 
subsequently processing - of [commands](../../notes/CQRS.md#commands). These 
commands are simple DTOs and can easily be serialised and logged.

## Recorded events
Every alteration in the state of the system leads to one or more [events](../../notes/CQRS.md#events). 
These events are simple DTOs and can easily be serialised and logged.
