"""北アルプス周辺の標高（AWS Open Data・Terrain Tiles／terrarium形式）を取得して1枚の標高配列にする"""
import math, io, urllib.request, numpy as np, os
from PIL import Image
Z = 11
def tile(lon, lat):
    n = 2 ** Z; x = (lon + 180) / 360 * n
    y = (1 - math.asinh(math.tan(math.radians(lat))) / math.pi) / 2 * n
    return int(x), int(y)
x0, y0 = tile(137.38, 36.62); x1, y1 = tile(138.02, 36.08)
rows = []
for ty in range(y0, y1 + 1):
    row = []
    for tx in range(x0, x1 + 1):
        p = f'/home/claude/web/gfx/dem/{Z}_{tx}_{ty}.png'
        if not os.path.exists(p):
            urllib.request.urlretrieve(f'https://s3.amazonaws.com/elevation-tiles-prod/terrarium/{Z}/{tx}/{ty}.png', p)
        a = np.asarray(Image.open(p).convert('RGB')).astype(float)
        row.append(a[..., 0] * 256 + a[..., 1] + a[..., 2] / 256 - 32768)
    rows.append(np.hstack(row))
E = np.vstack(rows); np.save('/home/claude/web/gfx/dem/alps.npy', E)
print((x1 - x0 + 1, y1 - y0 + 1), E.shape, round(E.min()), round(E.max()))
