# Authentication
Authentication refers to the process of verifying the authenticity of the 
request by identifying the sender.

## JWT
In this application, authentication is managed through [JSON Web Tokens](https://tools.ietf.org/html/rfc7519)
in `Authorization: Bearer ` request headers.

## Claims
- `account` - The identifier of the logged in [player account](../resource/account/overview/README.md)
