import json
import hashlib
from datetime import datetime, timezone, timedelta

JAKARTA_TZ = timezone(timedelta(hours=7))

def iso8601_now_jakarta() -> str:
    dt = datetime.now(JAKARTA_TZ)
    return dt.strftime("%Y-%m-%dT%H:%M:%S+07:00")

def compact_json(data: dict) -> str:
    return json.dumps(data, ensure_ascii=False, separators=(",", ":"))
