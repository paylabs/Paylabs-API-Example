import json
import math
from datetime import datetime, timezone, timedelta

JAKARTA_TZ = timezone(timedelta(hours=7))

def compact_json(data: dict) -> str:
    """Compact JSON: no spaces, unicode preserved, no escaped slashes."""
    return json.dumps(data, ensure_ascii=False, separators=(",", ":"))

def swatch_beats(dt_utc: datetime) -> str:
    """Compute PHP's date('B') value (Swatch Internet Time beats, 000-999).
    Spec: beats computed in BMT (UTC+1) as floor(seconds_since_midnight / 86.4).
    """
    bmt = dt_utc + timedelta(hours=1)
    seconds = (bmt.hour * 3600) + (bmt.minute * 60) + bmt.second
    beats = int(math.floor(seconds / 86.4)) % 1000
    return f"{beats:03d}"

def php_timestamp_with_B() -> str:
    """Mimic PHP: date("Y-m-d") . 'T' . date("H:i:s.B") . "+07:00" under Asia/Jakarta tz.
    Note: 'B' (beats) uses BMT (UTC+1), independent from tz, as in PHP.
    """
    now_jkt = datetime.now(JAKARTA_TZ)
    now_utc = now_jkt.astimezone(timezone.utc)
    B = swatch_beats(now_utc)
    return now_jkt.strftime("%Y-%m-%dT%H:%M:%S") + f".{B}+07:00"
