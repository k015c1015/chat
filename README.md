# Welcome

k015c1015/chat is a chat library for PHP.

# API Reference

## Receive comments (GET) -> JSON or XML or HTML
/api/receive/?tag=Seaman&startid=0&userid=12345678&type=html
- tag: String or *all
- startid: 0 to INF
- userid: 00000000 to 99999999
- type: JSON or XML or HTML

## Send comment (POST) -> JSON or XML or HTML
/api/send/
- tag: String
- startid: 0 to INF
- type: JSON or XML or HTML
- userid: 00000000 to 99999999
- message: String

## Get tags (GET) -> JSON or XML or HTML
/api/tags/?userid=12345678&type=json
- userid: 00000000 to 99999999
- type: JSON or XML or HTML

#### Regist user (GET) -> UserID
/api/regist/