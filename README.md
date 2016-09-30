# Welcome

k015c1015/chat is a chat API for PHP.

If you want to use, upload the /api folder to your server.

Enjoy! :p



# API Reference

## Regist user
[GET] **/api/regist/** -> User ID


## Get tags
[GET] **/api/tags/** -> JSON or XML or HTML

Params
- userid: 00000000 to 99999999
- type: JSON or XML or HTML


## Receive comments
[GET] **/api/receive/** -> JSON or XML or HTML

Params
- tag: String or *all
- startid: 0 to INF
- userid: 00000000 to 99999999
- type: JSON or XML or HTML


## Send comment
[POST] **/api/send/** -> JSON or XML or HTML

Params
- tag: String
- startid: 0 to INF
- type: JSON or XML or HTML
- userid: 00000000 to 99999999
- message: String