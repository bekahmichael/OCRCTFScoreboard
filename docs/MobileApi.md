# Mobile API

## Authorization Flow
```
  +--------+                                           +---------------+
  |        |--(A)------- Authorization Grant --------->|               |
  |        |                                           |               |
  |        |<-(B)----------- Access Token -------------|               |
  |        |               & Refresh Token             |               |
  |        |                                           |               |
  |        |                                           |               |
  |        |--(C)---- Access Token ------------------->|               |
  |        |                                           |               |
  |        |<-(D)- Protected Resource -----------------|    GateWay    |
  | Client |                                           |     Server    |
  |        |--(E)---- Access Token ------------------->|               |
  |        |                                           |               |
  |        |<-(F)- Invalid Token Error ----------------|               |
  |        |                                           |               |
  |        |                                           |               |
  |        |--(G)----------- Refresh Token ----------->|               |
  |        |                                           |               |
  |        |<-(H)----------- Access Token -------------|               |
  +--------+           & New Refresh Token             +---------------+
```


## Get *token* by _username_ and _password_
```bash
curl -X POST \
-H "Content-Type: application/json" \
--data '{"username":"admin@example.com", "password": "@pa$$pa$$"}' \
http://base.loc/api/mobile/v1/auth/token
```
```json
{
   "code":200,
   "success":true,
   "expires_in":86400,
   "access_token":"4vp5l6r0nOFpKXIdKUgHQ0e6tVpViqevNJKoh_MXNS-9CLafQelSG7NXVOEQ5tueYxtZ4UNxnz18XjKCj43MM92bzsaxypNlZurlD8vK6bza_5SCd8UTh0F6NWf4QPIn",
   "refresh_token":"_d-99UYd0sTT8AAsgGEWzIG4277iAk2ndPvFVgz71EELdqzH1TYt_FNBmh512guNlLV2ue8yEO8L4mGtcSOSAIsVheyWDhvnUCncuY53lnvZABICszegfUpLKuD_MQxI"
}
```

## View your basic profile info
```bash
curl -X GET \
-H 'Authorization: Bearer {ACCESS_TOKEN}' \
-H "Content-Type: application/json" \
http://base.loc/api/mobile/v1/auth/profile
```
```json
{
   "code":200,
   "data":{
      "username":"admin",
      "email":"admin@example.com",
      "status":"active",
      "first_name":"Admin",
      "last_name":"Admin"
   }
}
```